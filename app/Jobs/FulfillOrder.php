<?php

namespace App\Jobs;

use App\Actions\Orders\FulfillPaidOrder;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

/**
 * Turns a paid order into a learner account, a license and a purchase mail (see FulfillPaidOrder).
 * Dispatched by the payment webhook only after the order moved to `paid`; unique per order, so two copies
 * never run side by side. When every retry is used up the order is parked for an admin.
 */
class FulfillOrder implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    /** @return list<int> */
    public function backoff(): array
    {
        return [60, 300, 900, 3600];
    }

    public function __construct(public readonly string $orderId) {}

    public function uniqueId(): string
    {
        return $this->orderId;
    }

    public function handle(FulfillPaidOrder $fulfil): void
    {
        $order = Order::find($this->orderId);

        if ($order !== null) {
            $fulfil->handle($order);
        }
    }

    public function failed(Throwable $e): void
    {
        // The class name only: a message could carry data from a downstream service.
        Order::find($this->orderId)?->flagAttention('fulfil_failed', class_basename($e));
    }
}
