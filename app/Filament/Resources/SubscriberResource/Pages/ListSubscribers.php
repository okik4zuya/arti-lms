<?php

namespace App\Filament\Resources\SubscriberResource\Pages;

use App\Filament\Resources\SubscriberResource;
use App\Models\Subscriber;
use App\Support\SubscriberCsv;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListSubscribers extends ListRecords
{
    protected static string $resource = SubscriberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export')
                ->label('Ekspor CSV (aktif)')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->authorize('subscriber.manage')
                ->action(fn () => response()->streamDownload(function () {
                    $out = fopen('php://output', 'w');
                    SubscriberCsv::write($out);
                    fclose($out);
                }, 'daftar-email-'.now()->format('Ymd').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8'])),

            Actions\Action::make('syncBuyers')
                ->label('Cocokkan dengan pembeli')
                ->icon('heroicon-o-user-group')
                ->color('gray')
                ->authorize('subscriber.manage')
                ->requiresConfirmation()
                ->modalDescription('Alamat yang sama dengan email pembeli lisensi ditandai sebagai pembeli, supaya tidak menerima email penjualan. Lisensi baru ditandai otomatis; ini untuk yang lama.')
                ->action(function () {
                    $changed = Subscriber::syncBuyers();

                    Notification::make()
                        ->title($changed > 0 ? "{$changed} pendaftar ditandai sebagai pembeli." : 'Tidak ada yang berubah.')
                        ->success()
                        ->send();
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return SubscriberResource::getWidgets();
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return ['default' => 1, 'lg' => 2];
    }
}
