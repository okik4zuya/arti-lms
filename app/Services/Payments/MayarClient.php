<?php

namespace App\Services\Payments;

use App\Models\Order;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Mayar (v2) Single Payment Request: one hosted payment link per order, with `redirectUrl` back to our status page.
 *
 * - `orders.gateway_ref` is the payment request's `id` (Mayar has no field for our own order id). A lookup asks
 *   `GET /payments/{id}`; the order's public id is read back from our own table, never from Mayar.
 * - The API key lives in .env only. Nothing here logs a request or response body.
 * - Mayar's webhook carries no signature: the controller authenticates the URL with a secret path segment, treats the
 *   body only as a pointer, and settles from this client's status read.
 * - The method card on the landing form cannot narrow Mayar's page (`paymentMethod` only picks the code returned),
 *   so `$method` is ignored.
 */
class MayarClient implements PaymentGateway
{
    public function name(): string
    {
        return 'mayar';
    }

    public function label(): string
    {
        return 'Mayar';
    }

    public function company(): string
    {
        return 'Mayar';
    }

    public function isConfigured(): bool
    {
        return filled(config('services.mayar.api_key'));
    }

    /** The secret path segment of the webhook URL, compared in constant time. */
    public function validSecret(mixed $given): bool
    {
        $secret = (string) config('services.mayar.webhook_secret');

        return $secret !== '' && is_string($given) && hash_equals($secret, $given);
    }

    /**
     * Mayar's own "Webhook Token" (dashboard, Integration, API Keys & Token). Its header name is not documented, so any
     * request header value (bare or after "Bearer ") or a body `token` that equals it counts. An unset token skips the
     * check: the secret in the URL still protects the route.
     */
    public function validToken(Request $request): bool
    {
        $token = (string) config('services.mayar.webhook_token');

        if ($token === '') {
            return true;
        }

        $given = collect($request->headers->all())->flatten()->push($request->input('token'))
            ->filter(fn ($v) => is_string($v) && $v !== '')
            ->map(fn (string $v) => preg_replace('/^Bearer\s+/i', '', trim($v)));

        return $given->contains(fn (string $v) => hash_equals($token, $v));
    }

    public function createInvoice(Order $order, string $successUrl, string $failureUrl, int $durationSeconds = 3600, ?string $method = null): array
    {
        $payload = array_filter([
            'name' => 'ARTi Framework',
            'amount' => $order->amount,
            'email' => $order->email,
            'mobile' => $order->whatsapp ? preg_replace('/\D+/', '', $order->whatsapp) : null,
            'description' => 'Pesanan '.$order->public_id.' atas nama '.mb_substr($order->name, 0, 80),
            'redirectUrl' => $successUrl,
            'expiredAt' => now()->addSeconds($durationSeconds)->utc()->format('Y-m-d\TH:i:s.v\Z'),
        ], fn ($v) => $v !== null && $v !== '');

        $data = $this->send(fn (PendingRequest $http) => $http->post($this->apiUrl().'/payments/create', $payload))['data'] ?? [];

        if (! is_string($data['id'] ?? null) || ! is_string($data['link'] ?? null)) {
            throw new MayarException('bad_response', 'Mayar tidak mengembalikan halaman pembayaran.');
        }

        return ['id' => $data['id'], 'invoice_url' => $data['link'], 'status' => 'PENDING'];
    }

    public function getInvoice(string $reference): array
    {
        $publicId = Order::where('gateway', 'mayar')->where('gateway_ref', $reference)->value('public_id');

        if ($publicId === null) {
            throw new MayarException('not_found', 'Mayar: referensi ini tidak dikenal di pesanan kami.', 404);
        }

        $r = $this->send(fn (PendingRequest $http) => $http->get($this->apiUrl().'/payments/'.rawurlencode($reference)));

        return static::normalise((array) ($r['data'] ?? []), $reference, $publicId);
    }

    /**
     * Mayar's payment request into the shop's vocabulary. Statuses documented: `paid`, `unpaid`, `closed`. A closed
     * request (expired or shut by hand) can no longer be paid, so it is EXPIRED, which gives the launch place back.
     * The paid amount is the request's own amount: the detail's `transactions` list is not documented.
     *
     * @param  array<string, mixed>  $data
     * @return array{id:string,external_id:string,status:string,amount:int|float,paid_amount:int|float,payment_method:?string,payment_channel:?string}
     */
    public static function normalise(array $data, string $reference, string $publicId): array
    {
        $status = match (strtolower((string) ($data['status'] ?? ''))) {
            'paid', 'settled', 'success' => 'PAID',
            'closed', 'expired', 'failed' => 'EXPIRED',
            default => 'PENDING',
        };

        $amount = (int) round((float) ($data['amount'] ?? 0));
        $transaction = is_array($data['transactions'][0] ?? null) ? $data['transactions'][0] : [];
        $method = $transaction['paymentMethod'] ?? $transaction['paymentType'] ?? null;

        return [
            'id' => $reference,
            'external_id' => $publicId,
            'status' => $status,
            'amount' => $amount,
            'paid_amount' => $status === 'PAID' ? $amount : 0,
            'payment_method' => is_string($method) ? $method : null,
            'payment_channel' => null,
        ];
    }

    private function apiUrl(): string
    {
        return rtrim((string) config('services.mayar.api_url'), '/');
    }

    private function send(callable $call): array
    {
        if (! $this->isConfigured()) {
            throw new MayarException('not_configured', 'Mayar belum dikonfigurasi.');
        }

        $http = Http::withToken((string) config('services.mayar.api_key'))
            ->acceptJson()->asJson()->timeout(15)->connectTimeout(5);

        try {
            /** @var Response $response */
            $response = $call($http);
        } catch (ConnectionException) {
            throw new MayarException('unreachable', 'Mayar tidak dapat dihubungi.');
        }

        if ($response->failed()) {
            throw new MayarException('http_'.$response->status(), 'Mayar menolak permintaan.', $response->status());
        }

        // Some Mayar endpoints answer HTTP 200 with another code in the body: the body's statusCode is authoritative.
        $code = (int) $response->json('statusCode', 200);
        if ($code >= 400) {
            throw new MayarException('http_'.$code, 'Mayar menolak permintaan.', $code);
        }

        return (array) $response->json();
    }
}
