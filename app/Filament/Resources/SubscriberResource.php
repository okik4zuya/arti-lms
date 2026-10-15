<?php

namespace App\Filament\Resources;

use App\Actions\Subscribers\SendConfirmation;
use App\Filament\Resources\SubscriberResource\Pages;
use App\Filament\Resources\SubscriberResource\Widgets;
use App\Models\Subscriber;
use BackedEnum;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/** The mailing list (admin only): who signed up where, who confirmed, who left. Rows are made by the public forms. */
class SubscriberResource extends Resource
{
    protected static ?string $model = Subscriber::class;

    protected static ?string $modelLabel = 'pendaftar';

    protected static ?string $pluralModelLabel = 'Daftar email';

    protected static ?string $navigationLabel = 'Daftar email';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-envelope';

    protected static ?int $navigationSort = 20;

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('subscriber.manage') ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('subscriber.manage') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return static::canDelete(null);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('status')
                    ->state(fn (Subscriber $record) => $record->status())
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        Subscriber::ACTIVE => 'Aktif',
                        Subscriber::PENDING => 'Menunggu konfirmasi',
                        default => 'Berhenti',
                    })
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        Subscriber::ACTIVE => 'success',
                        Subscriber::PENDING => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('source')
                    ->label('Sumber')
                    ->formatStateUsing(fn (string $state) => Subscriber::SOURCES[$state] ?? $state)
                    ->sortable(),
                Tables\Columns\IconColumn::make('buyer_at')
                    ->label('Pembeli')
                    ->boolean()
                    ->state(fn (Subscriber $record) => $record->buyer_at !== null)
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-minus')
                    ->falseColor('gray'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Mendaftar')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('confirmed_at')
                    ->label('Dikonfirmasi')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('unsubscribed_at')
                    ->label('Berhenti')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('confirmation_sent_at')
                    ->label('Konfirmasi dikirim')
                    ->dateTime('d M Y H:i')
                    ->placeholder('belum')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->placeholder('Semua status')
                    ->options([
                        Subscriber::ACTIVE => 'Aktif',
                        Subscriber::PENDING => 'Menunggu konfirmasi',
                        Subscriber::UNSUBSCRIBED => 'Berhenti',
                    ])
                    ->query(fn (Builder $query, array $data) => match ($data['value'] ?? null) {
                        Subscriber::ACTIVE => $query->active(),
                        Subscriber::PENDING => $query->pending(),
                        Subscriber::UNSUBSCRIBED => $query->unsubscribed(),
                        default => $query,
                    }),
                Tables\Filters\SelectFilter::make('source')
                    ->label('Sumber')
                    ->placeholder('Semua sumber')
                    ->multiple()
                    ->options(Subscriber::SOURCES),
                Tables\Filters\TernaryFilter::make('buyer')
                    ->label('Pembeli')
                    ->placeholder('Semua')
                    ->trueLabel('Sudah membeli')
                    ->falseLabel('Belum membeli')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('buyer_at'),
                        false: fn (Builder $query) => $query->whereNull('buyer_at'),
                        blank: fn (Builder $query) => $query,
                    ),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(['default' => 1, 'md' => 3])
            ->deferFilters(false)
            ->recordActions([
                Actions\Action::make('resend')
                    ->label('Kirim ulang konfirmasi')
                    ->icon('heroicon-o-paper-airplane')
                    ->iconButton()
                    ->tooltip('Kirim ulang konfirmasi')
                    ->visible(fn (Subscriber $record) => $record->status() === Subscriber::PENDING)
                    ->requiresConfirmation()
                    ->modalDescription('Email konfirmasi dikirim lagi ke alamat ini lewat antrean.')
                    ->action(function (Subscriber $record) {
                        if (! $record->canResendConfirmation()) {
                            Notification::make()->title('Baru dikirim, tunggu '.Subscriber::RESEND_AFTER_MINUTES.' menit.')->warning()->send();

                            return;
                        }

                        $sent = app(SendConfirmation::class)->handle($record);

                        $sent
                            ? Notification::make()->title('Konfirmasi masuk antrean pengiriman.')->success()->send()
                            : Notification::make()->title('Tidak bisa memasukkan ke antrean. Periksa Redis dan log.')->danger()->send();
                    }),
                Actions\DeleteAction::make()
                    ->iconButton()
                    ->modalHeading('Hapus pendaftar')
                    ->modalDescription('Alamat ini dihapus permanen dari daftar, termasuk catatan persetujuannya. Cocok untuk permintaan penghapusan data.'),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()
                        ->modalDescription('Alamat yang dipilih dihapus permanen dari daftar.'),
                ]),
            ])
            ->emptyStateHeading('Belum ada pendaftar')
            ->emptyStateDescription('Pendaftar muncul di sini setelah mengisi formulir di halaman bab 1 gratis atau ARTi PDF.');
    }

    public static function getWidgets(): array
    {
        return [
            Widgets\SubscriberStats::class,
            Widgets\SubscribersBySource::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscribers::route('/'),
        ];
    }
}
