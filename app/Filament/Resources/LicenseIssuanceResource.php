<?php

namespace App\Filament\Resources;

use App\Actions\Licensing\ExtendLicense;
use App\Actions\Licensing\ReleaseDevice;
use App\Actions\Licensing\ResetAccountPassword;
use App\Actions\Licensing\RevokeLicense;
use App\Actions\Licensing\RotateKey;
use App\Actions\Licensing\SyncLicense;
use App\Filament\Resources\LicenseIssuanceResource\Pages;
use App\Models\LicenseIssuance;
use App\Services\Licensing\LicenseApi;
use App\Services\Licensing\LicenseApiException;
use App\Support\LicenseReasons;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use App\Support\LicenseHistory;
use App\Support\LicenseMessage;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Support\Enums\Width;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\URL;

class LicenseIssuanceResource extends Resource
{
    protected static ?string $model = LicenseIssuance::class;

    protected static ?string $modelLabel = 'lisensi';

    protected static ?string $pluralModelLabel = 'Lisensi';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-key';

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('license.view') ?? false;
    }

    /** Admins see how many manual licenses still wait for a payment check. */
    public static function getNavigationBadge(): ?string
    {
        if (! (auth()->user()?->can('payment.verify'))) {
            return null;
        }

        $count = LicenseIssuance::needsReview()->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

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
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Columns\TextColumn::make('payment')
                    ->label('Pembayaran')
                    ->state(fn (LicenseIssuance $record) => match ($record->paymentState()) {
                        'verified' => 'Terverifikasi',
                        'waiting' => 'Menunggu verifikasi',
                        'missing' => 'Bukti belum ada',
                        default => '-',
                    })
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'Terverifikasi' => 'success',
                        'Menunggu verifikasi' => 'warning',
                        'Bukti belum ada' => 'danger',
                        default => 'gray',
                    })
                    ->description(fn (LicenseIssuance $record) => $record->paymentState() === 'missing' && $record->created_at?->lt(now()->subDays(3))
                        ? (int) $record->created_at->diffInDays(now()).' hari tanpa bukti'
                        : null),
                Tables\Columns\TextColumn::make('activations')
                    ->label('Aktivasi')
                    ->state(fn (LicenseIssuance $record) => $record->activations_count.' / '.($record->seats ?? '-')),
                Tables\Columns\TextColumn::make('issuer.name')
                    ->label('Diterbitkan oleh')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('last_synced_at')
                    ->label('Sinkron terakhir')
                    ->since()
                    ->color(fn (LicenseIssuance $record) => $record->last_synced_at === null || $record->last_synced_at->lt(now()->subDays(2)) ? 'warning' : null)
                    ->tooltip(fn (LicenseIssuance $record) => $record->sync_error)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('key_id')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('order_ref')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('amount_idr')
                    ->label('Harga')
                    ->money('IDR', locale: 'id')
                    ->visible(fn () => auth()->user()?->can('license.view_amount') ?? false)
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
                    ->placeholder('Semua status')
                    ->options([
                        LicenseIssuance::PENDING => 'pending',
                        LicenseIssuance::ACTIVE => 'active',
                        LicenseIssuance::REVOKED => 'revoked',
                        LicenseIssuance::EXPIRED => 'expired',
                        LicenseIssuance::MISSING => 'missing_in_worker',
                    ]),
                Tables\Filters\Filter::make('needs_review')
                    ->label('Perlu ditinjau')
                    ->toggle()
                    ->query(fn (Builder $query) => $query->needsReview()),
                Tables\Filters\Filter::make('expiring')
                    ->label('Segera berakhir')
                    ->toggle()
                    ->query(fn (Builder $query) => $query->where('status', LicenseIssuance::ACTIVE)->whereBetween('expires_at', [now(), now()->addDays(30)])),
                Tables\Filters\Filter::make('stale_key')
                    ->label('Key basi')
                    ->toggle()
                    ->query(fn (Builder $query) => $query->where('key_stale', true)),
                Tables\Filters\Filter::make('wa_unsent')
                    ->label('WA belum dikirim')
                    ->toggle()
                    ->query(fn (Builder $query) => $query->where('status', LicenseIssuance::ACTIVE)->whereNotNull('key')->whereNull('wa_sent_at')),
                Tables\Filters\Filter::make('sync_failed')
                    ->label('Sinkron gagal')
                    ->toggle()
                    ->query(fn (Builder $query) => $query->whereNotNull('sync_error')),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(['default' => 2, 'md' => 3, 'lg' => 6])
            ->deferFilters(false)
            ->recordActions([
                static::sendKeyAction()->iconButton()->tooltip('Key / WhatsApp'),
                Actions\Action::make('sync')
                    ->authorize('license.sync')
                    ->label(fn (LicenseIssuance $record) => $record->isPending() ? 'Coba lagi' : 'Sinkronkan')
                    ->icon('heroicon-o-arrow-path')
                    ->iconButton()
                    ->tooltip(fn (LicenseIssuance $record) => $record->isPending() ? 'Coba lagi' : 'Sinkronkan')
                    ->action(function (LicenseIssuance $record): void {
                        try {
                            app(SyncLicense::class)->handle($record, verifyKey: true);
                            Notification::make()->title('Disinkronkan dengan Worker')->success()->send();
                        } catch (LicenseApiException $e) {
                            Notification::make()->title('Gagal menyinkronkan')->body($e->getMessage())->danger()->send();
                        }
                    }),
                Actions\ActionGroup::make([
                    static::extendAction(),
                    static::releaseDeviceAction(),
                    static::rotateKeyAction(),
                    static::resetAccountPasswordAction(),
                    static::attachProofAction(),
                    static::viewProofAction(),
                    static::verifyPaymentAction(),
                    static::markReviewedAction(),
                    static::editContactAction(),
                    static::historyAction(),
                ])->label('Kelola')->icon('heroicon-m-ellipsis-vertical')->button(),
                Actions\ActionGroup::make([
                    static::revokeAction(),
                    static::undoRevokeAction(),
                    static::deletePendingAction(),
                ])->label('Bahaya')->icon('heroicon-o-exclamation-triangle')->color('danger')->iconButton()->tooltip('Bahaya'),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\BulkAction::make('syncSelected')
                        ->authorize('license.sync')
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
            ->authorize('license.reveal_key')
            ->label('Key / WhatsApp')
            ->icon('heroicon-o-paper-airplane')
            ->visible(fn (LicenseIssuance $record) => $record->hasUsableKey())
            ->modalHeading(fn (LicenseIssuance $record) => 'Kirim key: '.$record->email)
            ->modalDescription('Key ini rahasia. Salin pesan di bawah dan kirim ke pembeli, lalu tandai sudah dikirim.')
            ->modalSubmitActionLabel('Tandai sudah dikirim')
            ->modalCancelActionLabel('Tutup')
            ->fillForm(function (LicenseIssuance $record, array $arguments): array {
                $record->logEvent('key_revealed', auth()->id());

                return [
                    'key' => $record->key,
                    // A learner password only exists in the arguments of the modal opened right after issuing or resetting.
                    'message' => LicenseMessage::for($record, $arguments['account_password'] ?? null),
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
                    ->url(fn (LicenseIssuance $record, $livewire) => LicenseMessage::url($record, $livewire->mountedActions[0]['arguments']['account_password'] ?? null))
                    ->openUrlInNewTab()
                    ->visible(fn (LicenseIssuance $record) => filled($record->buyer_whatsapp)),
            ])
            ->action(function (LicenseIssuance $record): void {
                $record->forceFill(['wa_sent_at' => now()])->save();
                $record->logEvent('message_sent', auth()->id());

                Notification::make()->title('Ditandai sudah dikirim')->success()->send();
            });
    }

    /**
     * Runs a license action and turns the three failure kinds into notifications; the modal stays open
     * (halt) so nothing typed is lost. Returns true when the action succeeded.
     */
    private static function attempt(Actions\Action $action, callable $do): bool
    {
        try {
            $do();

            return true;
        } catch (AuthorizationException) {
            $title = 'Tidak diizinkan';
            $body = 'Akun Anda tidak punya izin untuk aksi ini.';
        } catch (InvalidArgumentException $e) {
            $title = 'Tidak bisa diproses';
            $body = $e->getMessage();
        } catch (LicenseApiException $e) {
            $title = 'Worker menolak atau tidak terjangkau';
            $body = $e->getMessage().($e->isUnreachable() ? ' Hasilnya tidak pasti: Sinkronkan dan periksa datanya sebelum mengulang.' : '');
        }

        Notification::make()->title($title)->body($body)->danger()->persistent()->send();
        $action->halt();

        return false;
    }

    /** Preset reason plus free text; "Lainnya" requires the text (checked again server-side). */
    private static function reasonFields(array $reasons, string $label = 'Alasan'): array
    {
        return [
            Forms\Components\Select::make('reason')->label($label)->options(LicenseReasons::options($reasons))->required()->live(),
            Forms\Components\Textarea::make('detail')->label('Keterangan')->rows(2)
                ->required(fn ($get) => $get('reason') === LicenseReasons::OTHER)
                ->helperText('Wajib bila alasannya "Lainnya".'),
        ];
    }

    public static function extendAction(): Actions\Action
    {
        return Actions\Action::make('extend')
            ->label('Perpanjang')
            ->icon('heroicon-o-calendar-days')
            ->authorize('license.extend')
            ->visible(fn (LicenseIssuance $record) => ! $record->isPending() && $record->key_id !== null)
            ->modalHeading(fn (LicenseIssuance $record) => 'Perpanjang: '.$record->email)
            ->modalDescription(fn (LicenseIssuance $record) => 'Berlaku sampai '.($record->expires_at?->translatedFormat('j F Y') ?? '-').'. Tombol dikunci selama proses; jangan klik dua kali.')
            ->modalWidth(Width::Large)
            ->modalSubmitActionLabel('Perpanjang')
            ->fillForm(['kind' => ExtendLicense::PAID, 'months' => 12])
            ->schema([
                Forms\Components\Radio::make('kind')->label('Jenis')->inline()->required()->live()
                    ->options([ExtendLicense::PAID => 'Berbayar', ExtendLicense::COMPENSATION => 'Kompensasi (maks 1 bulan)'])
                    ->afterStateUpdated(fn ($state, $set) => $set('months', $state === ExtendLicense::COMPENSATION ? 1 : 12)),
                Forms\Components\TextInput::make('months')->label('Tambah (bulan)')->numeric()->required()->minValue(1)->maxValue(ExtendLicense::MAX_MONTHS),
                Forms\Components\TextInput::make('payment_ref')->label('Referensi pembayaran')->maxLength(255)
                    ->helperText('Boleh menyusul; tanpa referensi, baris masuk "Perlu ditinjau".')
                    ->visible(fn ($get) => $get('kind') === ExtendLicense::PAID),
                Group::make(static::reasonFields(LicenseReasons::EXTEND_COMPENSATION, 'Alasan kompensasi'))
                    ->visible(fn ($get) => $get('kind') === ExtendLicense::COMPENSATION),
            ])
            ->action(function (LicenseIssuance $record, array $data, Actions\Action $action): void {
                $done = static::attempt($action, fn () => app(ExtendLicense::class)->handle(
                    $record, auth()->user(), $data['kind'], (int) $data['months'], $data['payment_ref'] ?? null, $data['reason'] ?? null, $data['detail'] ?? null,
                ));

                if ($done) {
                    Notification::make()->title('Lisensi diperpanjang sampai '.$record->fresh()->expires_at->translatedFormat('j F Y'))->success()->send();
                }
            });
    }

    public static function releaseDeviceAction(): Actions\Action
    {
        return Actions\Action::make('releaseDevice')
            ->label('Lepas perangkat')
            ->icon('heroicon-o-computer-desktop')
            ->authorize('license.release_device')
            ->visible(fn (LicenseIssuance $record) => ! $record->isPending() && $record->key_id !== null)
            ->modalHeading(fn (LicenseIssuance $record) => 'Lepas perangkat: '.$record->email)
            ->modalDescription(fn (LicenseIssuance $record) => 'Pelepasan tahun ini: '.app(ReleaseDevice::class)->releasedThisYear($record).' dari '.ReleaseDevice::YEARLY_CAP.' (selebihnya lewat admin).')
            ->modalWidth(Width::Large)
            ->modalSubmitActionLabel('Lepaskan')
            ->schema(fn (LicenseIssuance $record) => [
                Forms\Components\Select::make('machine')->label('Perangkat')->required()
                    ->options(static::activeMachines($record))
                    ->helperText('Diambil langsung dari Worker.'),
                ...static::reasonFields(LicenseReasons::RELEASE_DEVICE),
            ])
            ->action(function (LicenseIssuance $record, array $data, Actions\Action $action): void {
                if (static::attempt($action, fn () => app(ReleaseDevice::class)->handle($record, auth()->user(), $data['machine'], $data['reason'] ?? null, $data['detail'] ?? null))) {
                    Notification::make()->title('Perangkat dilepas')->success()->send();
                }
            });
    }

    /** @return array<string,string> full machine hash => readable label, active machines only */
    private static function activeMachines(LicenseIssuance $record): array
    {
        try {
            $state = app(LicenseApi::class)->show((string) $record->key_id);
        } catch (LicenseApiException) {
            return [];
        }

        $options = [];

        foreach ($state['activations'] ?? [] as $a) {
            if (! empty($a['deactivated']) || blank($a['machine_hash'] ?? null)) {
                continue;
            }

            $hash = (string) $a['machine_hash'];
            $seen = ! empty($a['last_seen']) ? ', terakhir '.Carbon::parse($a['last_seen'])->translatedFormat('j M Y') : '';
            $options[$hash] = substr($hash, 0, 4).'...'.substr($hash, -4).(filled($a['label'] ?? null) ? " ({$a['label']})" : '').$seen;
        }

        return $options;
    }

    public static function rotateKeyAction(): Actions\Action
    {
        return Actions\Action::make('rotateKey')
            ->label('Ganti key')
            ->icon('heroicon-o-key')
            ->authorize('license.rotate_key')
            ->visible(fn (LicenseIssuance $record) => ! $record->isPending() && $record->key_id !== null)
            ->modalHeading(fn (LicenseIssuance $record) => 'Ganti key: '.$record->email)
            ->modalDescription('Key lama langsung berhenti berlaku di Worker. Bila masalahnya ganti komputer, pakai "Lepas perangkat" dulu; key tidak perlu diganti.')
            ->modalWidth(Width::Large)
            ->modalSubmitActionLabel('Ganti key')
            ->schema(static::reasonFields(LicenseReasons::ROTATE_KEY))
            ->action(function (LicenseIssuance $record, array $data, Actions\Action $action, $livewire): void {
                if (static::attempt($action, fn () => app(RotateKey::class)->handle($record, auth()->user(), $data['reason'] ?? null, $data['detail'] ?? null))) {
                    Notification::make()->title('Key diganti. Kirim key baru ke pembeli.')->success()->send();
                    static::openKeyModal($livewire, $record);
                }
            });
    }

    /**
     * Opens the key/WhatsApp modal in place of the table action that just ran. `replaceMountedTableAction`
     * only stacks a table action on top of another table action, and Filament cannot resolve that, so the
     * stack is cleared and the modal mounted afresh.
     */
    private static function openKeyModal($livewire, LicenseIssuance $record, ?string $accountPassword = null): void
    {
        $livewire->replaceMountedAction(
            'sendKey',
            $accountPassword === null ? [] : ['account_password' => $accountPassword],
            ['table' => true, 'recordKey' => (string) $record->getKey()],
        );
    }

    /** New random password for the buyer's learner account; shown once in the key message, never stored. */
    public static function resetAccountPasswordAction(): Actions\Action
    {
        return Actions\Action::make('resetAccountPassword')
            ->label('Reset password akun')
            ->icon('heroicon-o-lock-closed')
            ->authorize('account.reset_password')
            ->visible(fn (LicenseIssuance $record) => $record->user_id !== null)
            ->requiresConfirmation()
            ->modalHeading(fn (LicenseIssuance $record) => 'Reset password akun: '.$record->email)
            ->modalDescription('Password lama diganti dan sesi lama diputus. Password baru hanya tampil sekali, di pesan yang terbuka sesudahnya; tidak disimpan.')
            ->modalSubmitActionLabel('Reset password')
            ->action(function (LicenseIssuance $record, Actions\Action $action, $livewire): void {
                $password = null;

                if (! static::attempt($action, function () use ($record, &$password) {
                    $password = app(ResetAccountPassword::class)->handle($record, auth()->user());
                })) {
                    return;
                }

                if ($record->hasUsableKey()) {
                    Notification::make()->title('Password direset. Kirim pesan berisi password baru.')->success()->send();
                    static::openKeyModal($livewire, $record, $password);

                    return;
                }

                // No usable key to build the message around: show the password once, in a notification only.
                Notification::make()->title('Password baru: '.$password)->body('Salin sekarang, tidak ditampilkan lagi.')->success()->persistent()->send();
            });
    }

    public static function revokeAction(): Actions\Action
    {
        return Actions\Action::make('revoke')
            ->label('Cabut lisensi')
            ->icon('heroicon-o-no-symbol')
            ->color('danger')
            ->authorize('license.revoke')
            ->visible(fn (LicenseIssuance $record) => ! $record->isPending() && $record->key_id !== null && $record->status !== LicenseIssuance::REVOKED)
            ->modalHeading(fn (LicenseIssuance $record) => 'Cabut lisensi: '.$record->email)
            ->modalDescription('Menghentikan perpanjangan dan aktivasi baru. Mesin yang sudah aktif tidak dimatikan: lisensinya berlaku sampai tanggal berakhir.')
            ->modalWidth(Width::Large)
            ->modalSubmitActionLabel('Cabut')
            ->schema(fn (LicenseIssuance $record) => [
                ...static::reasonFields(LicenseReasons::REVOKE),
                Forms\Components\TextInput::make('confirm_email')->label('Ketik email untuk konfirmasi')->required()->placeholder($record->email),
            ])
            ->action(function (LicenseIssuance $record, array $data, Actions\Action $action): void {
                if (static::attempt($action, fn () => app(RevokeLicense::class)->handle($record, auth()->user(), $data['reason'] ?? null, $data['detail'] ?? null, $data['confirm_email'] ?? null))) {
                    Notification::make()->title('Lisensi dicabut')->success()->send();
                }
            });
    }

    public static function undoRevokeAction(): Actions\Action
    {
        return Actions\Action::make('undoRevoke')
            ->label('Batalkan pencabutan')
            ->icon('heroicon-o-arrow-uturn-left')
            ->authorize('license.revoke')
            ->visible(fn (LicenseIssuance $record) => $record->status === LicenseIssuance::REVOKED)
            ->modalHeading(fn (LicenseIssuance $record) => 'Batalkan pencabutan: '.$record->email)
            ->modalWidth(Width::Large)
            ->modalSubmitActionLabel('Batalkan pencabutan')
            ->schema(static::reasonFields(LicenseReasons::UNDO_REVOKE))
            ->action(function (LicenseIssuance $record, array $data, Actions\Action $action): void {
                if (static::attempt($action, fn () => app(RevokeLicense::class)->handle($record, auth()->user(), $data['reason'] ?? null, $data['detail'] ?? null, undo: true))) {
                    Notification::make()->title('Pencabutan dibatalkan')->success()->send();
                }
            });
    }

    /** Only a row that never reached the Worker (pending, no key_id) can be deleted; a license is only ever revoked. */
    public static function deletePendingAction(): Actions\Action
    {
        return Actions\Action::make('deletePending')
            ->label('Hapus baris pending')
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->authorize('license.delete_pending')
            ->visible(fn (LicenseIssuance $record) => $record->isPending() && $record->key_id === null)
            ->requiresConfirmation()
            ->modalHeading(fn (LicenseIssuance $record) => 'Hapus baris pending: '.$record->email)
            ->modalDescription('Baris ini belum pernah terbit di Worker. Menghapusnya tidak bisa dibatalkan.')
            ->action(function (LicenseIssuance $record, Actions\Action $action): void {
                if ($record->key_id !== null || ! $record->isPending()) {
                    Notification::make()->title('Hanya baris pending tanpa key yang bisa dihapus')->danger()->send();
                    $action->halt();

                    return;
                }

                Log::info('License pending row deleted', ['order_ref' => $record->order_ref, 'email' => $record->email, 'by' => auth()->id()]);
                $record->delete();

                Notification::make()->title('Baris pending dihapus')->success()->send();
            });
    }

    /** Admin only: clears the review note (free license, compensation, unpaid extension) once looked at. */
    public static function markReviewedAction(): Actions\Action
    {
        return Actions\Action::make('markReviewed')
            ->label('Tandai ditinjau')
            ->icon('heroicon-o-eye')
            ->authorize('payment.verify')
            ->visible(fn (LicenseIssuance $record) => filled($record->review_note))
            ->modalHeading(fn (LicenseIssuance $record) => 'Tandai ditinjau: '.$record->email)
            ->modalDescription(fn (LicenseIssuance $record) => (string) $record->review_note)
            ->modalSubmitActionLabel('Tandai ditinjau')
            ->action(function (LicenseIssuance $record): void {
                $record->logEvent('reviewed', auth()->id(), $record->review_note);
                $record->forceFill(['review_note' => null])->save();

                Notification::make()->title('Ditandai sudah ditinjau')->success()->send();
            });
    }

    /** A payment reference and/or a proof file (jpg, png, pdf, 5 MB) on the private disk; may come after issuing. */
    public static function attachProofAction(): Actions\Action
    {
        return Actions\Action::make('attachProof')
            ->label('Lampirkan bukti')
            ->icon('heroicon-o-paper-clip')
            ->authorize('payment.attach')
            ->modalHeading(fn (LicenseIssuance $record) => 'Lampirkan bukti: '.$record->email)
            ->visible(fn (LicenseIssuance $record) => ! $record->isPending() && $record->payment_verified_at === null)
            ->modalWidth(Width::Large)
            ->modalSubmitActionLabel('Simpan')
            ->fillForm(fn (LicenseIssuance $record) => ['payment_ref' => $record->payment_ref])
            ->schema([
                Forms\Components\TextInput::make('payment_ref')->label('Referensi pembayaran')->maxLength(255),
                static::proofUpload()->label('Bukti pembayaran'),
            ])
            ->action(function (LicenseIssuance $record, array $data, Actions\Action $action): void {
                if (blank($data['payment_ref'] ?? null) && blank($data['payment_proof'] ?? null)) {
                    Notification::make()->title('Isi referensi atau unggah berkas')->danger()->send();
                    $action->halt();

                    return;
                }

                $record->forceFill(array_filter([
                    'payment_ref' => $data['payment_ref'] ?? null,
                    'payment_proof_path' => $data['payment_proof'] ?? null,
                ], fn ($v) => filled($v)))->save();
                $record->logEvent('payment_attached', auth()->id());

                Notification::make()->title('Bukti disimpan')->success()->send();
            });
    }

    public static function proofUpload(): Forms\Components\FileUpload
    {
        return Forms\Components\FileUpload::make('payment_proof')
            ->disk(config('filesystems.proofs_disk'))
            ->directory('license-proofs')
            ->visibility('private')
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])
            ->maxSize(5120)
            ->helperText('jpg, png atau pdf, maksimal 5 MB.');
    }

    /** Opens the proof through a signed, five-minute link; the file itself is never publicly addressable. */
    public static function viewProofAction(): Actions\Action
    {
        return Actions\Action::make('viewProof')
            ->label('Lihat bukti')
            ->icon('heroicon-o-document-magnifying-glass')
            ->authorize('license.view')
            ->visible(fn (LicenseIssuance $record) => filled($record->payment_proof_path))
            ->url(fn (LicenseIssuance $record) => URL::temporarySignedRoute('license.proof', now()->addMinutes(5), ['issuance' => $record->getKey()]))
            ->openUrlInNewTab();
    }

    /** Admin only: the second pair of eyes (maker-checker) on a payment support has recorded. */
    public static function verifyPaymentAction(): Actions\Action
    {
        return Actions\Action::make('verifyPayment')
            ->label('Verifikasi pembayaran')
            ->icon('heroicon-o-check-badge')
            ->authorize('payment.verify')
            ->modalHeading(fn (LicenseIssuance $record) => 'Verifikasi pembayaran: '.$record->email)
            ->visible(fn (LicenseIssuance $record) => ! $record->isPending() && $record->payment_verified_at === null)
            ->requiresConfirmation()
            ->modalDescription('Pastikan dana benar-benar masuk dan sesuai harga.')
            ->action(function (LicenseIssuance $record): void {
                $record->forceFill(['payment_verified_by' => auth()->id(), 'payment_verified_at' => now()])->save();
                $record->logEvent('payment_verified', auth()->id());

                Notification::make()->title('Pembayaran terverifikasi')->success()->send();
            });
    }

    /** Name, WhatsApp and notes; the email is the Worker's identity for the license and stays fixed. */
    public static function editContactAction(): Actions\Action
    {
        return Actions\Action::make('editContact')
            ->label('Ubah kontak')
            ->icon('heroicon-o-pencil-square')
            ->authorize('license.edit_contact')
            ->modalHeading(fn (LicenseIssuance $record) => 'Ubah kontak: '.$record->email)
            ->modalWidth(Width::Large)
            ->modalSubmitActionLabel('Simpan')
            ->fillForm(fn (LicenseIssuance $record) => $record->only(['name', 'buyer_whatsapp', 'notes']))
            ->schema([
                Forms\Components\TextInput::make('name')->label('Nama')->required()->maxLength(255),
                Forms\Components\TextInput::make('buyer_whatsapp')->label('WhatsApp pembeli')->tel()->maxLength(30),
                Forms\Components\Textarea::make('notes')->label('Catatan')->rows(2),
            ])
            ->action(function (LicenseIssuance $record, array $data): void {
                $record->fill($data)->save();
                $record->logEvent('contact_edited', auth()->id());

                Notification::make()->title('Kontak diperbarui')->success()->send();
            });
    }

    /** Read-only slide-over: summary, activations and events (Worker plus local), fetched live from the Worker. */
    public static function historyAction(): Actions\Action
    {
        return Actions\Action::make('history')
            ->label('Riwayat')
            ->icon('heroicon-o-clock')
            ->authorize('license.view')
            ->slideOver()
            ->modalWidth(Width::Large)
            ->modalHeading(fn (LicenseIssuance $record) => 'Riwayat: '.$record->email)
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Tutup')
            ->modalContent(fn (LicenseIssuance $record) => view('filament.license-history', LicenseHistory::for($record)));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLicenseIssuances::route('/'),
        ];
    }
}
