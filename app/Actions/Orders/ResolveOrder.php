<?php

namespace App\Actions\Orders;

use App\Jobs\FulfillOrder;
use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use InvalidArgumentException;

/**
 * An admin's decision to go ahead with an order that was parked: honour a late payment, accept a second
 * purchase, or retry a fulfilment that ran out of attempts. Not offered for amount mismatches or staff emails:
 * those need a refund or a manual sale, not a retry.
 */
class ResolveOrder
{
    public const RESOLVABLE = ['fulfil_failed', 'duplicate_purchase', 'late_payment_no_place'];

    public function handle(Order $order, User $actor): void
    {
        if (! $actor->can('order.resolve')) {
            throw new AuthorizationException;
        }

        if ($order->status !== Order::NEEDS_ATTENTION || ! in_array($order->attention_note, self::RESOLVABLE, true)) {
            throw new InvalidArgumentException('Pesanan ini tidak bisa dilanjutkan otomatis. Kembalikan dananya atau tangani manual.');
        }

        // 'approved' tells fulfilment to skip the duplicate check this one time.
        $moved = $order->transition([Order::NEEDS_ATTENTION], Order::PAID, ['attention_note' => 'approved', 'paid_at' => $order->paid_at ?? now()]);

        if ($moved) {
            $order->logEvent('resolved', 'continued after review', $actor->getKey());
            FulfillOrder::dispatch($order->id);
        }
    }
}
