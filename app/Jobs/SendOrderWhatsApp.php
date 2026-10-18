<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\WhatsApp\WhatsAppSender;
use App\Support\Offer;
use App\Support\OrderWhatsAppMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

/**
 * The WhatsApp copy of the purchase mail. A separate stage on purpose: it can fail, be switched off, or be
 * retried without touching the order, which is already fulfilled. The one-time link travels in the job
 * payload (it cannot be rebuilt: the token is stored hashed), so keep `queue:prune-failed` on the schedule.
 */
class SendOrderWhatsApp implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /** @return list<int> */
    public function backoff(): array
    {
        return [60, 600];
    }

    public function __construct(public readonly string $orderId, public readonly ?string $setPasswordUrl) {}

    public function handle(WhatsAppSender $sender): void
    {
        $order = Order::find($this->orderId);
        $number = Offer::normalizeNumber($order?->whatsapp);

        if ($order === null || $number === null || $order->licenseIssuance === null) {
            return;
        }

        if (! $sender->enabled()) {
            $order->logEvent('whatsapp_skipped', 'no provider configured');

            return;
        }

        $claimed = Order::whereKey($order->id)->whereNull('whatsapp_sent_at')->update(['whatsapp_sent_at' => now()]) === 1;

        if (! $claimed) {
            return;
        }

        try {
            $sender->send($number, OrderWhatsAppMessage::for($order, $this->setPasswordUrl));
        } catch (Throwable $e) {
            Order::whereKey($order->id)->update(['whatsapp_sent_at' => null]);

            throw $e;
        }

        $order->logEvent('whatsapp_sent');
    }

    public function failed(Throwable $e): void
    {
        // Never fails the order. The class name only: a provider message could echo the text.
        Order::find($this->orderId)?->logEvent('whatsapp_failed', class_basename($e));
    }
}
