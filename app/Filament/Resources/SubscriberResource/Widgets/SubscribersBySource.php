<?php

namespace App\Filament\Resources\SubscriberResource\Widgets;

use App\Models\Subscriber;
use Filament\Widgets\ChartWidget;

/** Which sign-up place brings people, and how many of them confirm. */
class SubscribersBySource extends ChartWidget
{
    protected ?string $heading = 'Pendaftar per sumber';

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '260px';

    public static function canView(): bool
    {
        return auth()->user()?->can('subscriber.manage') ?? false;
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $sources = array_keys(Subscriber::SOURCES);

        $count = fn (string $scope) => collect($sources)->map(
            fn (string $source) => Subscriber::query()->$scope()->where('source', $source)->count()
        )->all();

        return [
            'datasets' => [
                ['label' => 'Aktif', 'data' => $count('active'), 'backgroundColor' => '#16a34a'],
                ['label' => 'Menunggu konfirmasi', 'data' => $count('pending'), 'backgroundColor' => '#f59e0b'],
            ],
            'labels' => array_values(Subscriber::SOURCES),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => ['y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]]],
        ];
    }
}
