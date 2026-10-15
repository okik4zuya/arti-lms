<?php

namespace App\Filament\Resources\SubscriberResource\Widgets;

use App\Models\DownloadCount;
use App\Models\Subscriber;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SubscriberStats extends StatsOverviewWidget
{
    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = null;

    public static function canView(): bool
    {
        return auth()->user()?->can('subscriber.manage') ?? false;
    }

    protected function getStats(): array
    {
        $active = Subscriber::active()->count();
        $pending = Subscriber::pending()->count();
        $left = Subscriber::unsubscribed()->count();
        $buyers = Subscriber::active()->whereNotNull('buyer_at')->count();

        $days = collect(range(6, 0))->map(fn (int $ago) => today()->subDays($ago));
        $perDay = DownloadCount::query()
            ->where('name', 'artipdf')
            ->whereDate('day', '>=', $days->first())
            ->pluck('downloads', 'day')
            ->mapWithKeys(fn ($count, $day) => [substr((string) $day, 0, 10) => (int) $count]);
        $series = $days->map(fn ($day) => $perDay[$day->toDateString()] ?? 0)->all();

        return [
            Stat::make('Aktif', $active)
                ->description($buyers > 0 ? "{$buyers} di antaranya sudah membeli" : 'Boleh menerima email')
                ->color('success'),
            Stat::make('Menunggu konfirmasi', $pending)
                ->description('Belum menekan tautan di email')
                ->color($pending > 0 ? 'warning' : 'gray'),
            Stat::make('Berhenti', $left)
                ->description('Tidak akan dikirimi email')
                ->color('gray'),
            Stat::make('Unduhan ARTi PDF, 7 hari', array_sum($series))
                ->description('Hari ini: '.end($series))
                ->chart($series)
                ->color('primary'),
        ];
    }
}
