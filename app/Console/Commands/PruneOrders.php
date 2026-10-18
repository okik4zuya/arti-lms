<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

/**
 * Orders that were never paid (expired or failed) hold a name, an email and maybe a phone number for no purpose.
 * The privacy page promises they are removed within a year; this keeps the promise. Paid orders are never touched
 * (bookkeeping law keeps them for years).
 */
class PruneOrders extends Command
{
    protected $signature = 'orders:prune {--days=365 : Age after which unpaid orders are deleted}';

    protected $description = 'Delete expired or failed orders that were never paid and are older than a year';

    public function handle(): int
    {
        $days = max(30, (int) $this->option('days'));

        $count = Order::whereIn('status', [Order::EXPIRED, Order::FAILED])
            ->whereNull('paid_at')
            ->where('created_at', '<', now()->subDays($days))
            ->delete();

        $this->info("Deleted {$count} unpaid order(s) older than {$days} days.");

        return self::SUCCESS;
    }
}
