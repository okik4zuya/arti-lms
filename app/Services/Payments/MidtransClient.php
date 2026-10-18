<?php

namespace App\Services\Payments;

use App\Models\Order;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Midtrans Snap (hosted payment page: QRIS, virtual account, e-wallet, cards) and its Status API.
 *
 * - Our order's public id is Midtrans' `order_id`, and also what we store as `orders.gateway_ref` (Midtrans looks
 *   a payment up by order id), so a lookup never depends on a second identifier.
 * - The server key lives in .env only. Nothing here logs a request or response body.
 * - A webhook is never trusted for status or amount: its signature is checked, then the Status API is asked.
 */
class MidtransClient implements PaymentGateway
{
    public function name(): string
    {
        return 'midtrans';
    }

    public function label(): string
    {
        return 'Midtrans';
    }

    public function company(): string
    {
        return 'PT Midtrans';
    }

    public function isConfigured(): bool
    {
        return filled(config('services.midtrans.server_key'));
    }

    /**
     * `signature_key` of a notification: SHA-512 of order_id + status_code + gross_amount + server key, compared in
     * constant time. The three values are used exactly as Midtrans sent them (gross_amount is a string like "99000.00").
     */
    public function validSignature(mixed $orderId, mixed $statusCode, mixed $grossAmount, mixed $signature): bool
    {
        $key = (string) config('services.midtrans.server_key');

        if ($key === '' || ! is_string($orderId) || ! is_string($statusCode) || ! is_string($grossAmount) || ! is_string($signature)) {
            return false;
        }

        return hash_equals(hash('sha512', $orderId.$statusCode.$grossAmount.$key), strtolower($signature));
    }

    public function createInvoice(Order $order, string $successUrl, string $failureUrl, int $durationSeconds = 3600): array
    {
        $payload = [
            'transaction_details' => ['order_id' => $order->public_id, 'gross_amount' => $order->amount],
            'item_details' => [['id' => 'arti-framework', 'price' => $order->amount, 'quantity' => 1, 'name' => 'ARTi Framework']],
            'customer_details' => array_filter([
                'first_name' => mb_substr($order->name, 0, 50),
                'email' => $order->email,
                'phone' => $order->whatsapp ? '+'.ltrim($order->whatsapp, '+') : null,
            ]),
            'expiry' => ['unit' => 'minutes', 'duration' => max(1, (int) ceil($durationSeconds / 60))],
            'callbacks' => ['finish' => $successUrl, 'error' => $failureUrl, 'pending' => $successUrl],
        ];

        $response = $this->send(fn (PendingRequest $http) => $http->post($this->snapUrl().'/snap/v1/transactions', $payload));

        if (! is_string($response['redirect_url'] ?? null)) {
            throw new MidtransException('bad_response', 'Midtrans tidak mengembalikan halaman pembayaran.');
        }

        return ['id' => $order->public_id, 'invoice_url' => $response['redirect_url'], 'status' => 'PENDING'];
    }

    public function getInvoice(string $reference): array
    {
        $r = $this->send(fn (PendingRequest $http) => $http->get($this->apiUrl().'/v2/'.rawurlencode($reference).'/status'));

        // Midtrans answers an unknown order with HTTP 200 (or 404) and status_code "404" in the body.
        if ((string) ($r['status_code'] ?? '') === '404') {
            throw new MidtransException('not_found', 'Midtrans tidak mengenal pesanan ini.', 404);
        }

        return static::normalise($r);
    }

    /**
     * Midtrans' transaction_status into the shop's vocabulary. Only a settled payment (or a card capture that
     * passed fraud screening) is PAID; every unpaid end is EXPIRED, which gives the launch place back.
     *
     * @param  array<string, mixed>  $r
     * @return array{id:string,external_id:string,status:string,amount:int|float,paid_amount:int|float,payment_method:?string,payment_channel:?string}
     */
    public static function normalise(array $r): array
    {
        $transaction = (string) ($r['transaction_status'] ?? '');
        $fraud = (string) ($r['fraud_status'] ?? 'accept');

        $status = match (true) {
            $transaction === 'settlement' => 'PAID',
            $transaction === 'capture' && $fraud === 'accept' => 'PAID',
            in_array($transaction, ['expire', 'cancel', 'deny', 'failure'], true) => 'EXPIRED',
            default => 'PENDING', // pending, authorize, a challenged capture; refunds and chargebacks are an admin matter
        };

        $amount = (int) round((float) ($r['gross_amount'] ?? 0));
        $channel = $r['va_numbers'][0]['bank'] ?? $r['store'] ?? $r['issuer'] ?? $r['acquirer'] ?? $r['bank'] ?? null;

        return [
            'id' => (string) ($r['order_id'] ?? ''),
            'external_id' => (string) ($r['order_id'] ?? ''),
            'status' => $status,
            'amount' => $amount,
            'paid_amount' => $status === 'PAID' ? $amount : 0,
            'payment_method' => isset($r['payment_type']) ? (string) $r['payment_type'] : null,
            'payment_channel' => is_string($channel) ? $channel : null,
        ];
    }

    private function snapUrl(): string
    {
        return rtrim((string) config('services.midtrans.snap_url'), '/');
    }

    private function apiUrl(): string
    {
        return rtrim((string) config('services.midtrans.api_url'), '/');
    }

    private function send(callable $call): array
    {
        if (! $this->isConfigured()) {
            throw new MidtransException('not_configured', 'Midtrans belum dikonfigurasi.');
        }

        $http = Http::withBasicAuth((string) config('services.midtrans.server_key'), '')
            ->acceptJson()->asJson()->timeout(15)->connectTimeout(5);

        try {
            /** @var Response $response */
            $response = $call($http);
        } catch (ConnectionException) {
            throw new MidtransException('unreachable', 'Midtrans tidak dapat dihubungi.');
        }

        // Midtrans reports many errors with HTTP 4xx and a JSON body; 404 is kept so "not found" can be told apart.
        if ($response->failed() && $response->status() !== 404) {
            throw new MidtransException('http_'.$response->status(), 'Midtrans menolak permintaan.', $response->status());
        }

        return (array) $response->json();
    }
}
