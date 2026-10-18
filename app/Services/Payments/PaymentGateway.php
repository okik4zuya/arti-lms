<?php

namespace App\Services\Payments;

use App\Models\Order;

/**
 * What the shop needs from a payment gateway, whichever one is configured (PAYMENT_GATEWAY). Both drivers speak the
 * same normalised language so orders, quota and fulfilment never know which gateway took the money:
 * an "invoice" array with `id` (the reference stored in orders.gateway_ref), `external_id` (our order's public id),
 * `status` (PAID, SETTLED, EXPIRED or anything else for "still open"), `amount`, `paid_amount`, `payment_method`,
 * `payment_channel`. Status and amount always come from the gateway's own API, never from a webhook body.
 */
interface PaymentGateway
{
    /** Stored in orders.gateway: `midtrans` or `xendit`. */
    public function name(): string;

    /** Shown to buyers ("Midtrans"). */
    public function label(): string;

    /** The legal entity that processes the payment, named on the privacy page. */
    public function company(): string;

    public function isConfigured(): bool;

    /**
     * Create the hosted payment page for an order.
     *
     * @return array{id:string,invoice_url:string,status?:string}
     *
     * @throws PaymentGatewayException
     */
    public function createInvoice(Order $order, string $successUrl, string $failureUrl, int $durationSeconds = 3600): array;

    /**
     * The authoritative, normalised state of a payment, looked up by the reference stored in orders.gateway_ref.
     *
     * @return array{id:string,external_id:string,status:string,amount:int|float,paid_amount?:int|float,payment_method?:string,payment_channel?:string}
     *
     * @throws PaymentGatewayException (`status` 404 when the gateway does not know the reference)
     */
    public function getInvoice(string $reference): array;
}
