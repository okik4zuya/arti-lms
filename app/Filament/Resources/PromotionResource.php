<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromotionResource\Pages;
use App\Models\Promotion;
use App\Support\Quota;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Event promotions: a named price with a real end date and an optional quota. The cheapest running promotion
 * with a place left beats the launch price (see Offer::tier()). Admin only.
 */
class PromotionResource extends Resource
{
    protected static ?string $model = Promotion::class;

    protected static ?string $modelLabel = 'promo';

    protected static ?string $pluralModelLabel = 'Promo';

    protected static ?string $navigationLabel = 'Promo';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static ?int $navigationSort = 7;

    public static function canAccess(): bool
    {
        return auth()->user()?->can('site.manage') ?? false;
    }

    /** A promotion that has orders stays as a record of what was sold; switch it off instead. */
    public static function canDelete($record): bool
    {
        return (auth()->user()?->can('site.manage') ?? false) && ! $record->orders()->exists();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\TextInput::make('name')
                ->label('Nama internal')
                ->required()->maxLength(60)->unique(ignoreRecord: true)
                ->helperText('Untuk Anda sendiri, contoh: hari-peneliti-2026. Tidak tampil di situs.'),
            Forms\Components\TextInput::make('label')
                ->label('Nama yang tampil di halaman')
                ->required()->maxLength(120)
                ->helperText('Contoh: "Promo Hari Peneliti". Jangan memakai kata "perintis": itu hanya untuk batch pertama.'),
            Forms\Components\TextInput::make('price')
                ->label('Harga promo (Rp)')
                ->numeric()->integer()->required()->minValue(10000)
                ->rule(fn () => function (string $attribute, mixed $value, \Closure $fail) {
                    if ((int) $value >= (int) config('site.offer.price_normal')) {
                        $fail('Harga promo harus lebih rendah dari harga normal (Rp'.number_format((int) config('site.offer.price_normal'), 0, ',', '.').').');
                    }
                }),
            Forms\Components\DateTimePicker::make('starts_at')->label('Mulai')->required()->seconds(false),
            Forms\Components\DateTimePicker::make('ends_at')->label('Berakhir')->required()->seconds(false)->after('starts_at')
                ->helperText('Tanggal berakhir wajib dan tampil di halaman. Sesudahnya harga kembali otomatis.'),
            Forms\Components\TextInput::make('quota')
                ->label('Kuota (kosongkan bila tanpa batas)')
                ->numeric()->integer()->minValue(1)->maxValue(1000),
            Forms\Components\Toggle::make('active')->label('Aktif')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('starts_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('label')->label('Promo')->searchable(),
                Tables\Columns\TextColumn::make('price')->label('Harga')->formatStateUsing(fn ($state) => 'Rp'.number_format((int) $state, 0, ',', '.')),
                Tables\Columns\TextColumn::make('starts_at')->label('Mulai')->dateTime('d M Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('ends_at')->label('Berakhir')->dateTime('d M Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('state')->label('Status')->badge()
                    ->state(fn (Promotion $p) => static::state($p))
                    ->color(fn (string $state) => match ($state) {
                        'Berjalan' => 'success',
                        'Terjadwal' => 'info',
                        'Kuota habis' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('used')->label('Terpakai')
                    ->state(fn (Promotion $p) => $p->quota === null ? $p->orders()->count().' pesanan' : Quota::promoHeld($p).' dari '.$p->quota),
            ])
            ->recordActions([
                Actions\EditAction::make()->iconButton(),
                Actions\DeleteAction::make()->iconButton()->visible(fn (Promotion $p) => static::canDelete($p)),
            ])
            ->emptyStateHeading('Belum ada promo')
            ->emptyStateDescription('Buat promo untuk event: harga khusus dengan tanggal berakhir yang nyata.');
    }

    public static function state(Promotion $p): string
    {
        return match (true) {
            ! $p->active => 'Nonaktif',
            $p->ends_at->isPast() => 'Selesai',
            $p->starts_at->isFuture() => 'Terjadwal',
            $p->quota !== null && Quota::promoHeld($p) >= $p->quota => 'Kuota habis',
            default => 'Berjalan',
        };
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPromotions::route('/'),
            'create' => Pages\CreatePromotion::route('/create'),
            'edit' => Pages\EditPromotion::route('/{record}/edit'),
        ];
    }
}
