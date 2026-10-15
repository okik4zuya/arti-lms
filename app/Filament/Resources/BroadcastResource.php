<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BroadcastResource\Pages;
use App\Jobs\SendBroadcast;
use App\Mail\BroadcastMail;
use App\Models\Broadcast;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;
use Throwable;

/** One-off emails to the mailing list (admin only). Written in Markdown; a draft can be tested, then sent once. */
class BroadcastResource extends Resource
{
    protected static ?string $model = Broadcast::class;

    protected static ?string $modelLabel = 'siaran';

    protected static ?string $pluralModelLabel = 'Siaran email';

    protected static ?string $navigationLabel = 'Siaran email';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-megaphone';

    protected static ?int $navigationSort = 21;

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('subscriber.manage') ?? false;
    }

    public static function canCreate(): bool
    {
        return static::canViewAny();
    }

    /** Only a draft may still be changed or removed; a sent broadcast is a record of what went out. */
    public static function canEdit($record): bool
    {
        return static::canViewAny() && $record->isDraft();
    }

    public static function canDelete($record): bool
    {
        return static::canViewAny() && $record->isDraft();
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\TextInput::make('subject')
                ->label('Judul email')
                ->required()
                ->maxLength(150),
            Forms\Components\Select::make('audience')
                ->label('Dikirim ke')
                ->options(Broadcast::AUDIENCES)
                ->default(Broadcast::NON_BUYERS)
                ->required()
                ->native(false)
                ->helperText('Hanya alamat yang sudah konfirmasi dan belum berhenti yang pernah dihitung. Pilih "belum membeli" untuk penawaran, supaya pembeli tidak dikirimi iklan.'),
            Forms\Components\MarkdownEditor::make('body')
                ->label('Isi (Markdown)')
                ->required()
                ->minHeight('18rem')
                ->helperText('Tautan berhenti berlangganan dan alasan penerima mendapat email ditambahkan otomatis di bagian bawah. Tulis dengan suara ARTi: saya/Anda, jujur, tanpa tenggat palsu.')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('subject')
                    ->label('Judul')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('audience')
                    ->label('Dikirim ke')
                    ->formatStateUsing(fn (string $state) => Broadcast::AUDIENCES[$state] ?? $state),
                Tables\Columns\TextColumn::make('status')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        Broadcast::SENT => 'Terkirim',
                        Broadcast::SENDING => 'Sedang dikirim',
                        default => 'Draf',
                    })
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        Broadcast::SENT => 'success',
                        Broadcast::SENDING => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('recipients_count')
                    ->label('Penerima')
                    ->placeholder('-')
                    ->state(fn (Broadcast $record) => $record->status === Broadcast::DRAFT ? null : $record->recipients_count),
                Tables\Columns\TextColumn::make('sent_at')
                    ->label('Terkirim')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                Actions\Action::make('test')
                    ->label('Kirim uji ke saya')
                    ->icon('heroicon-o-beaker')
                    ->iconButton()
                    ->tooltip('Kirim uji ke saya')
                    ->action(function (Broadcast $record) {
                        $to = auth()->user()?->email;

                        try {
                            Mail::to($to)->queue(new BroadcastMail(null, $record, test: true));
                            Notification::make()->title("Email uji masuk antrean ke {$to}.")->success()->send();
                        } catch (Throwable $e) {
                            report($e);
                            Notification::make()->title('Tidak bisa memasukkan ke antrean. Periksa Redis dan log.')->danger()->send();
                        }
                    }),
                Actions\Action::make('send')
                    ->label(fn (Broadcast $record) => $record->status === Broadcast::SENDING ? 'Lanjutkan pengiriman' : 'Kirim')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->visible(fn (Broadcast $record) => $record->status !== Broadcast::SENT)
                    ->requiresConfirmation()
                    ->modalHeading(fn (Broadcast $record) => $record->status === Broadcast::SENDING ? 'Lanjutkan pengiriman?' : 'Kirim siaran ini?')
                    ->modalDescription(fn (Broadcast $record) => sprintf(
                        'Dikirim ke %d orang (%s). Yang sudah menerimanya tidak dikirimi lagi. Pengiriman tidak bisa dibatalkan.',
                        $record->recipients()->count(),
                        mb_strtolower(Broadcast::AUDIENCES[$record->audience] ?? $record->audience),
                    ))
                    ->action(function (Broadcast $record) {
                        // A broadcast that is already "sending" may have no one left: the job then only closes it.
                        if ($record->status === Broadcast::DRAFT && $record->recipients()->count() === 0) {
                            Notification::make()->title('Tidak ada penerima yang memenuhi syarat.')->warning()->send();

                            return;
                        }

                        $record->forceFill(['status' => Broadcast::SENDING])->save();
                        SendBroadcast::dispatch($record->id);

                        Notification::make()->title('Pengiriman dimulai. Penerima dimasukkan ke antrean email.')->success()->send();
                    }),
                // Hidden (and so not callable) once sent: a sent broadcast is the record of what went out.
                Actions\EditAction::make()->iconButton()->hidden(fn (Broadcast $record) => ! $record->isDraft()),
                Actions\DeleteAction::make()->iconButton()->hidden(fn (Broadcast $record) => ! $record->isDraft()),
            ])
            ->emptyStateHeading('Belum ada siaran')
            ->emptyStateDescription('Siaran dipakai untuk kabar sesekali: bab baru, batch harga berikutnya, atau pembaruan alat.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBroadcasts::route('/'),
            'create' => Pages\CreateBroadcast::route('/create'),
            'edit' => Pages\EditBroadcast::route('/{record}/edit'),
        ];
    }
}
