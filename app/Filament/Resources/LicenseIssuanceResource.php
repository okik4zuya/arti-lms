<?php

namespace App\Filament\Resources;

use App\Actions\Licensing\SyncLicense;
use App\Filament\Resources\LicenseIssuanceResource\Pages;
use App\Models\LicenseIssuance;
use App\Services\Licensing\LicenseApiException;
use App\Support\LicenseMessage;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class LicenseIssuanceResource extends Resource
{
    protected static ?string $model = LicenseIssuance::class;

    protected static ?string $modelLabel = 'lisensi';

    protected static ?string $pluralModelLabel = 'Lisensi';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-key';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->description(fn (LicenseIssuance $record) => $record->name),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->hidden(),
                Tables\Columns\TextColumn::make('tier')
                    ->badge(),
                Tables\Columns\TextColumn::make('key_state')
                    ->label('Key')
                    ->state(fn (LicenseIssuance $record) => match (true) {
                        $record->key_stale => 'Basi',
                        filled($record->key) => 'Tersimpan',
                        default => 'Tidak ada',
                    })
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'Tersimpan' => 'success',
                        'Basi' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        LicenseIssuance::ACTIVE => 'success',
                        LicenseIssuance::PENDING => 'warning',
                        default => 'danger',
                    }),
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Berlaku sampai')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('activations')
                    ->label('Aktivasi')
                    ->state(fn (LicenseIssuance $record) => $record->activations_count.' / '.($record->seats ?? '-')),
                Tables\Columns\TextColumn::make('last_synced_at')
                    ->label('Sinkron terakhir')
                    ->since()
                    ->color(fn (LicenseIssuance $record) => $record->last_synced_at === null || $record->last_synced_at->lt(now()->subDays(2)) ? 'warning' : null)
                    ->tooltip(fn (LicenseIssuance $record) => $record->sync_error),
                Tables\Columns\TextColumn::make('key_id')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('order_ref')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('amount_idr')
                    ->label('Harga')
                    ->money('IDR', locale: 'id')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('wa_sent_at')
                    ->label('WA terkirim')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        LicenseIssuance::PENDING => 'pending',
                        LicenseIssuance::ACTIVE => 'active',
                        LicenseIssuance::REVOKED => 'revoked',
                        LicenseIssuance::EXPIRED => 'expired',
                        LicenseIssuance::MISSING => 'missing_in_worker',
                    ]),
            ])
            ->recordActions([
                static::sendKeyAction(),
                Actions\Action::make('sync')
                    ->label(fn (LicenseIssuance $record) => $record->isPending() ? 'Coba lagi' : 'Sinkronkan')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function (LicenseIssuance $record): void {
                        try {
                            app(SyncLicense::class)->handle($record, verifyKey: true);
                            Notification::make()->title('Disinkronkan dengan Worker')->success()->send();
                        } catch (LicenseApiException $e) {
                            Notification::make()->title('Gagal menyinkronkan')->body($e->getMessage())->danger()->send();
                        }
                    }),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\BulkAction::make('syncSelected')
                        ->label('Sinkronkan terpilih')
                        ->icon('heroicon-o-arrow-path')
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records): void {
                            $failed = 0;

                            foreach ($records as $record) {
                                try {
                                    app(SyncLicense::class)->handle($record);
                                } catch (LicenseApiException) {
                                    $failed++;
                                }
                            }

                            Notification::make()
                                ->title($failed ? "{$failed} dari {$records->count()} gagal disinkronkan" : 'Disinkronkan dengan Worker')
                                ->color($failed ? 'warning' : 'success')
                                ->send();
                        }),
                ]),
            ]);
    }

    /** Shows the stored key and the ready-to-send WhatsApp message; every opening is logged. */
    public static function sendKeyAction(): Actions\Action
    {
        return Actions\Action::make('sendKey')
            ->label('Key / WhatsApp')
            ->icon('heroicon-o-paper-airplane')
            ->visible(fn (LicenseIssuance $record) => $record->hasUsableKey())
            ->modalHeading('Kirim key ke pembeli')
            ->modalDescription('Key ini rahasia. Salin pesan di bawah dan kirim ke pembeli, lalu tandai sudah dikirim.')
            ->modalSubmitActionLabel('Tandai sudah dikirim')
            ->modalCancelActionLabel('Tutup')
            ->fillForm(function (LicenseIssuance $record): array {
                $record->logEvent('key_revealed', auth()->id());

                return [
                    'key' => $record->key,
                    'message' => LicenseMessage::for($record),
                ];
            })
            ->schema([
                Forms\Components\TextInput::make('key')
                    ->label('Key')
                    ->readOnly()
                    ->copyable(copyMessage: 'Key disalin'),
                Forms\Components\Textarea::make('message')
                    ->label('Pesan WhatsApp')
                    ->rows(12)
                    ->readOnly()
                    ->helperText('Pilih semua teks lalu salin, atau gunakan tombol Buka WhatsApp bila nomor pembeli diisi.'),
            ])
            ->extraModalFooterActions([
                Actions\Action::make('openWhatsapp')
                    ->label('Buka WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->url(fn (LicenseIssuance $record) => LicenseMessage::url($record))
                    ->openUrlInNewTab()
                    ->visible(fn (LicenseIssuance $record) => filled($record->buyer_whatsapp)),
            ])
            ->action(function (LicenseIssuance $record): void {
                $record->forceFill(['wa_sent_at' => now()])->save();
                $record->logEvent('message_sent', auth()->id());

                Notification::make()->title('Ditandai sudah dikirim')->success()->send();
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLicenseIssuances::route('/'),
        ];
    }
}
