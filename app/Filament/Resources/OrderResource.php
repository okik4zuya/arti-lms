<?php

namespace App\Filament\Resources;

use App\Actions\Orders\RefundOrder;
use App\Actions\Orders\ResendPurchaseMail;
use App\Actions\Orders\ResolveOrder;
use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use BackedEnum;
use Filament\Actions;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\Width;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Throwable;

/**
 * Orders from the payment gateway. Rows are made by the checkout and the webhook; nobody edits them by hand.
 * Support may look and re-send the purchase mail; refunds and parked orders are for admins. Never shows a key,
 * a set-password link or a token.
 */
class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $modelLabel = 'pesanan';

    protected static ?string $pluralModelLabel = 'Pesanan';

    protected static ?string $navigationLabel = 'Pesanan';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?int $navigationSort = 5;

    public const STATUS_LABELS = [
        Order::PENDING => 'Menunggu bayar',
        Order::PAID => 'Dibayar',
        Order::FULFILLING => 'Diproses',
        Order::FULFILLED => 'Selesai',
        Order::EXPIRED => 'Kedaluwarsa',
        Order::FAILED => 'Gagal',
        Order::REFUNDED => 'Dikembalikan',
        Order::NEEDS_ATTENTION => 'Perlu dicek',
    ];

    public const NOTE_LABELS = [
        'amount_mismatch' => 'Jumlah bayar tidak sama',
        'duplicate_purchase' => 'Sudah punya lisensi aktif',
        'late_payment_no_place' => 'Bayar terlambat, harga habis',
        'fulfil_failed' => 'Pemrosesan gagal',
        'staff_email' => 'Email milik admin/support',
        'course_missing' => 'Kursus belum ada',
        'approved' => 'Dilanjutkan admin',
    ];

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('order.view') ?? false;
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
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        if (! (auth()->user()?->can('order.resolve'))) {
            return null;
        }

        $count = Order::where('status', Order::NEEDS_ATTENTION)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label('Dibuat')->dateTime('d M Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('public_id')->label('Referensi')->copyable()->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')->label('Nama')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('amount')->label('Jumlah')->formatStateUsing(fn ($state) => 'Rp'.number_format((int) $state, 0, ',', '.'))->sortable(),
                Tables\Columns\TextColumn::make('price_tier')->label('Harga')->badge()
                    ->formatStateUsing(fn (string $state) => ['launch' => 'Perintis', 'promo' => 'Promo', 'normal' => 'Normal'][$state] ?? $state)
                    ->color(fn (string $state) => ['launch' => 'info', 'promo' => 'warning'][$state] ?? 'gray'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->formatStateUsing(fn (string $state) => self::STATUS_LABELS[$state] ?? $state)
                    ->color(fn (string $state) => match ($state) {
                        Order::FULFILLED => 'success',
                        Order::PAID, Order::FULFILLING => 'info',
                        Order::NEEDS_ATTENTION, Order::FAILED => 'danger',
                        Order::PENDING => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('attention_note')->label('Catatan')
                    ->formatStateUsing(fn (?string $state) => self::NOTE_LABELS[$state] ?? $state)->placeholder('-'),
                Tables\Columns\TextColumn::make('payment_method')->label('Metode')->placeholder('-')->toggleable(),
                Tables\Columns\TextColumn::make('paid_at')->label('Dibayar')->dateTime('d M Y H:i')->placeholder('-')->sortable(),
                Tables\Columns\IconColumn::make('email_sent_at')->label('Email')->boolean()->state(fn (Order $r) => $r->email_sent_at !== null)
                    ->trueIcon('heroicon-o-check-circle')->falseIcon('heroicon-o-minus')->falseColor('gray'),
                Tables\Columns\IconColumn::make('whatsapp_sent_at')->label('WA')->boolean()->state(fn (Order $r) => $r->whatsapp_sent_at !== null)
                    ->trueIcon('heroicon-o-check-circle')->falseIcon('heroicon-o-minus')->falseColor('gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->placeholder('Semua status')->multiple()->options(self::STATUS_LABELS),
                Tables\Filters\SelectFilter::make('price_tier')->label('Harga')->placeholder('Semua')
                    ->options(['launch' => 'Perintis', 'promo' => 'Promo', 'normal' => 'Normal']),
                Tables\Filters\Filter::make('attention')->label('Perlu dicek')
                    ->query(fn ($query) => $query->where('status', Order::NEEDS_ATTENTION)->orWhereNotNull('attention_note')),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(['default' => 1, 'md' => 3])
            ->deferFilters(false)
            ->recordActions([
                Actions\Action::make('history')
                    ->label('Riwayat')
                    ->icon('heroicon-o-clock')
                    ->iconButton()
                    ->tooltip('Riwayat pesanan')
                    ->modalHeading(fn (Order $record) => 'Riwayat '.$record->public_id)
                    ->modalWidth(Width::Large)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalContent(fn (Order $record) => view('filament.order-history', ['order' => $record, 'events' => $record->events()->limit(100)->get()])),

                Actions\Action::make('resend')
                    ->label('Kirim ulang email')
                    ->icon('heroicon-o-paper-airplane')
                    ->iconButton()
                    ->tooltip('Kirim ulang email pembelian dengan tautan atur password baru')
                    ->authorize('order.resend')
                    ->visible(fn (Order $record) => $record->status === Order::FULFILLED)
                    ->requiresConfirmation()
                    ->modalDescription('Email pembelian dikirim lagi dengan tautan atur password yang baru. Tautan lama berhenti berlaku.')
                    ->action(function (Order $record) {
                        try {
                            app(ResendPurchaseMail::class)->handle($record, auth()->user());
                            Notification::make()->title('Email masuk antrean pengiriman.')->success()->send();
                        } catch (Throwable $e) {
                            Notification::make()->title($e->getMessage())->danger()->send();
                        }
                    }),

                Actions\Action::make('resolve')
                    ->label('Lanjutkan')
                    ->icon('heroicon-o-play')
                    ->iconButton()
                    ->tooltip('Lanjutkan pemrosesan pesanan ini')
                    ->authorize('order.resolve')
                    ->visible(fn (Order $record) => $record->status === Order::NEEDS_ATTENTION && in_array($record->attention_note, ResolveOrder::RESOLVABLE, true))
                    ->requiresConfirmation()
                    ->modalDescription('Pesanan diproses seperti pembayaran biasa: akun, lisensi, dan email dibuat. Pastikan Anda sudah memeriksa penyebabnya.')
                    ->action(function (Order $record) {
                        try {
                            app(ResolveOrder::class)->handle($record, auth()->user());
                            Notification::make()->title('Pemrosesan dilanjutkan.')->success()->send();
                        } catch (Throwable $e) {
                            Notification::make()->title($e->getMessage())->danger()->send();
                        }
                    }),

                Actions\Action::make('refund')
                    ->label('Kembalikan dana')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->iconButton()
                    ->color('danger')
                    ->tooltip('Tandai dikembalikan: cabut lisensi dan akses kursus')
                    ->authorize('order.refund')
                    ->visible(fn (Order $record) => in_array($record->status, [Order::PAID, Order::FULFILLING, Order::FULFILLED, Order::NEEDS_ATTENTION], true))
                    ->modalHeading('Kembalikan dana')
                    ->modalDescription('Lisensi dicabut di Worker dan akses kursus dihapus. Uangnya sendiri dikembalikan lewat dashboard penyedia pembayaran; sistem tidak memanggil penyedia itu.')
                    ->modalWidth(Width::Large)
                    ->schema([
                        Textarea::make('reason')->label('Alasan')->required()->maxLength(250)->rows(2),
                        TextInput::make('confirm_email')->label('Ketik email pembeli untuk memastikan')->email()->required(),
                    ])
                    ->action(function (Order $record, array $data) {
                        try {
                            app(RefundOrder::class)->handle($record, auth()->user(), $data['reason'], $data['confirm_email']);
                            Notification::make()->title('Pesanan ditandai dikembalikan. Kembalikan uangnya di dashboard penyedia pembayaran.')->success()->send();
                        } catch (Throwable $e) {
                            Notification::make()->title($e->getMessage())->danger()->send();
                        }
                    }),
            ])
            ->emptyStateHeading('Belum ada pesanan')
            ->emptyStateDescription('Pesanan muncul di sini setelah ada yang menekan "Beli sekarang" di situs.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
        ];
    }
}
