<?php

namespace App\Console\Commands;

use App\Actions\Orders\SettleInvoice;
use App\Jobs\FulfillOrder;
use App\Models\Order;
use App\Services\Payments\PaymentGatewayException;
use App\Services\Payments\PaymentGateways;
use App\Support\Quota;
use Illuminate\Console\Command;

/**
 * Catches what the webhook missed (Laravel was down, the gateway gave up retrying) and what a crashed job left behind.
 * Asks the order's own gateway about every order still waiting, and applies the answer through the same code the webhook uses.
 * Payments are checked BEFORE expired orders give their places back, so a buyer who paid in the last minute of
 * the window is not turned away.
 */
class ReconcileOrders extends Command
{
    protected $signature = 'orders:reconcile {--dry-run : Report only, change nothing}';

    protected $description = 'Check unpaid orders against the payment gateway, expire the lapsed ones and restart stuck fulfilment';

    public function handle(PaymentGateways $gateways, SettleInvoice $settle): int
    {
        if (! $gateways->current()->isConfigured()) {
            $this->warn('The payment gateway is not configured; nothing to reconcile.');

            return self::SUCCESS;
        }

        $dry = (bool) $this->option('dry-run');
        $counts = ['checked' => 0, 'paid' => 0, 'expired' => 0, 'unreachable' => 0, 'restarted' => 0];

        // Waiting orders, plus orders we expired in the last 3 days that may have been paid late. Skip the very
        // newest ones: their webhook is probably on its way.
        $waiting = Order::whereIn('status', [Order::PENDING, Order::EXPIRED])
            ->whereNotNull('gateway_ref')
            ->where('created_at', '<=', now()->subMinutes(3))
            ->where('created_at', '>=', now()->subDays(3))
            ->get();

        foreach ($waiting as $order) {
            $counts['checked']++;

            try {
                $invoice = $gateways->for($order->gateway)->getInvoice($order->gateway_ref);
            } catch (PaymentGatewayException $e) {
                $counts['unreachable']++;
                $this->warn("{$order->public_id}: the gateway said ".$e->errorCode);

                continue;
            }

            $status = $invoice['status'] ?? null;
            $this->line("{$order->public_id}: local {$order->status}, gateway {$status}");

            if ($dry) {
                continue;
            }

            $before = $order->status;
            $settle->handle($order, $invoice);
            $order->refresh();

            if ($before !== $order->status && $order->status === Order::PAID) {
                $counts['paid']++;
                $order->logEvent('reconciled', 'paid');
            }
        }

        if (! $dry) {
            $counts['expired'] = Quota::releaseExpired();
        }

        // Paid but fulfilment never finished (job lost, worker down for long): run it again. Safe by construction.
        $stuck = Order::whereIn('status', [Order::PAID, Order::FULFILLING])->where('updated_at', '<=', now()->subMinutes(10))->get();

        foreach ($stuck as $order) {
            $this->line("{$order->public_id}: stuck in {$order->status}, fulfilment restarted");
            if (! $dry) {
                FulfillOrder::dispatch($order->id);
                $counts['restarted']++;
            }
        }

        $this->info(sprintf(
            '%s: checked %d, newly paid %d, expired %d, restarted %d, gateway unreachable %d.',
            $dry ? 'Dry run' : 'Done', $counts['checked'], $counts['paid'], $counts['expired'], $counts['restarted'], $counts['unreachable'],
        ));

        return self::SUCCESS;
    }
}
