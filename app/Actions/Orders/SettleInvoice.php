<?php

namespace App\Actions\Orders;

use App\Jobs\FulfillOrder;
use App\Models\Order;
use App\Support\Offer;
use App\Support\Quota;

/**
 * Applies the payment gateway's own account of an invoice to an order. Used by the payment webhook and by orders:reconcile,
 * so a callback that arrives and a callback that was missed end in exactly the same state. The caller must pass
 * an invoice it read from the gateway's API, never the body of a request.
 *
 * Every state change is a compare-and-set (Order::transition), so running this twice, or from two places at
 * once, fulfils an order once.
 */
class SettleInvoice
{
    /**
     * @param  array<string, mixed>  $invoice
     * @return bool false when the invoice does not belong to the order (nothing was touched)
     */
    public function handle(Order $order, array $invoice): bool
    {
        if (($invoice['external_id'] ?? null) !== $order->public_id
            || ($order->gateway_ref !== null && $order->gateway_ref !== ($invoice['id'] ?? null))) {
            $order->logEvent('callback_mismatch', 'invoice does not belong to this order');

            return false;
        }

        match ($invoice['status'] ?? null) {
            'PAID', 'SETTLED' => $this->paid($order, $invoice),
            'EXPIRED' => $this->expired($order),
            default => null,
        };

        return true;
    }

    /** @param  array<string, mixed>  $invoice */
    private function paid(Order $order, array $invoice): void
    {
        $paidAmount = (int) round((float) ($invoice['paid_amount'] ?? $invoice['amount'] ?? 0));

        if ((int) round((float) ($invoice['amount'] ?? 0)) !== $order->amount || $paidAmount !== $order->amount) {
            if (in_array($order->status, [Order::PENDING, Order::EXPIRED], true)) {
                $order->flagAttention('amount_mismatch', "expected {$order->amount}, paid {$paidAmount}");
            }

            return;
        }

        // Paid after the window closed: its place was given back, so take one again. If none is left the
        // buyer has paid a price that no longer exists, which an admin decides (honour it or refund).
        $offer = app(Offer::class);
        if ($order->status === Order::EXPIRED && ! Quota::reclaim($order, $offer->quota() - $offer->manualSold())) {
            $order->flagAttention('late_payment_no_place', 'paid after expiry and no place is left at the original price');

            return;
        }

        $changed = $order->transition([Order::PENDING, Order::EXPIRED], Order::PAID, array_filter([
            'paid_at' => now(),
            'gateway_ref' => $order->gateway_ref ?? ($invoice['id'] ?? null),
            'payment_method' => $this->method($invoice),
        ]));

        if ($changed) {
            $order->logEvent('paid', $this->method($invoice));
            FulfillOrder::dispatch($order->id);
        }
    }

    private function expired(Order $order): void
    {
        if ($order->transition([Order::PENDING], Order::EXPIRED, ['launch_slot' => null, 'promo_slot' => null])) {
            $order->logEvent('expired');
        }
    }

    /** @param  array<string, mixed>  $invoice */
    private function method(array $invoice): ?string
    {
        $method = trim(($invoice['payment_method'] ?? '').' '.($invoice['payment_channel'] ?? ''));

        return $method === '' ? null : mb_substr($method, 0, 60);
    }
}
