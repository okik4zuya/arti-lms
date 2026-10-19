<?php

namespace App\Services\Payments;

use App\Models\Order;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Xendit Invoice API (hosted payment page: QRIS, virtual account, e-wallet). The secret key and the
 * callback token live in .env only. Nothing here logs a request or response body.
 */
class XenditClient implements PaymentGateway
{
    public function name(): string
    {
        return 'xendit';
    }

    public function label(): string
    {
        return 'Xendit';
    }

    public function company(): string
    {
        return 'PT Sinar Digital Terdepan';
    }

    public function isConfigured(): bool
    {
        return filled(config('services.xendit.secret_key')) && filled(config('services.xendit.callback_token'));
    }

    /** Constant-time check of the `x-callback-token` header. Fails closed when no token is configured. */
    public function validCallbackToken(?string $given): bool
    {
        $expected = (string) config('services.xendit.callback_token');

        return $expected !== '' && is_string($given) && hash_equals($expected, $given);
    }

    /**
     * Create the invoice for an order. The order's public id is the `external_id`.
     *
     * @return array{id:string,invoice_url:string,status:string,expiry_date?:string}
     */
    public function createInvoice(Order $order, string $successUrl, string $failureUrl, int $durationSeconds = 3600, ?string $method = null): array
    {
        return $this->send(fn (PendingRequest $http) => $http->post('/v2/invoices', array_filter([
            'external_id' => $order->public_id,
            'amount' => $order->amount,
            'currency' => 'IDR',
            'description' => 'ARTi Framework',
            'payer_email' => $order->email,
            'customer' => array_filter(['given_names' => $order->name, 'email' => $order->email, 'mobile_number' => $order->whatsapp ? '+'.ltrim($order->whatsapp, '+') : null]),
            'invoice_duration' => $durationSeconds,
            'success_redirect_url' => $successUrl,
            'failure_redirect_url' => $failureUrl,
        ], fn ($v) => $v !== null && $v !== [])));
    }

    /**
     * The authoritative state of an invoice. Webhooks are never trusted on their own body: the status and
     * amount used to settle an order come from this call.
     *
     * @return array{id:string,external_id:string,status:string,amount:int|float,paid_amount?:int|float,payment_method?:string,payment_channel?:string,paid_at?:string}
     */
    public function getInvoice(string $invoiceId): array
    {
        return $this->send(fn (PendingRequest $http) => $http->get('/v2/invoices/'.rawurlencode($invoiceId)));
    }

    private function send(callable $call): array
    {
        if (! $this->isConfigured()) {
            throw new XenditException('not_configured', 'Xendit belum dikonfigurasi.');
        }

        $http = Http::baseUrl(rtrim((string) config('services.xendit.url'), '/'))
            ->withBasicAuth((string) config('services.xendit.secret_key'), '')
            ->acceptJson()->asJson()->timeout(15)->connectTimeout(5);

        try {
            /** @var Response $response */
            $response = $call($http);
        } catch (ConnectionException) {
            throw new XenditException('unreachable', 'Xendit tidak dapat dihubungi.');
        }

        if ($response->failed()) {
            throw new XenditException(
                (string) ($response->json('error_code') ?? 'http_'.$response->status()),
                'Xendit menolak permintaan.',
                $response->status(),
            );
        }

        return (array) $response->json();
    }
}
