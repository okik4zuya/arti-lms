<?php

namespace App\Filament\Resources\LicenseIssuanceResource\Pages;

use App\Actions\Account\IssueSetPasswordLink;
use App\Actions\Learners\CreateLearner;
use App\Actions\Licensing\IssueLicense;
use App\Actions\Licensing\IssueRequest;
use App\Filament\Resources\LicenseIssuanceResource;
use App\Models\Course;
use App\Models\LicenseIssuance;
use App\Models\User;
use App\Services\Licensing\LicenseApi;
use App\Services\Licensing\LicenseApiException;
use App\Support\Offer;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ListLicenseIssuances extends ListRecords
{
    protected static string $resource = LicenseIssuanceResource::class;

    public function getSubheading(): string|Htmlable|null
    {
        if (! app(LicenseApi::class)->isConfigured()) {
            return 'Worker lisensi belum dikonfigurasi: isi LICENSE_API_URL dan LICENSE_ADMIN_TOKEN di .env.';
        }

        $offer = app(Offer::class);

        return 'Pembeli: '.LicenseIssuance::sold()->count().' dari kuota harga peluncuran '.$offer->quota().'.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('issue')
                ->label('Terbitkan lisensi')
                ->icon('heroicon-o-plus')
                ->disabled(fn () => ! app(LicenseApi::class)->isConfigured())
                ->authorize('license.issue')
                ->modalHeading('Terbitkan lisensi baru')
                ->modalWidth(Width::TwoExtraLarge)
                ->stickyModalHeader()
                ->modalSubmitActionLabel('Terbitkan')
                ->fillForm(fn () => [
                    'months' => 12,
                    'seats' => 1,
                    'amount_idr' => $this->suggestedPrice(),
                    'order_ref' => 'WA-'.now()->format('ymd').'-'.Str::upper(Str::random(4)),
                ])
                ->schema([
                    Grid::make(6)->schema([
                        Forms\Components\TextInput::make('name')->label('Nama')->required()->maxLength(255)->columnSpan(3),
                        Forms\Components\TextInput::make('email')->email()->required()->maxLength(255)->columnSpan(3),
                        Forms\Components\TextInput::make('buyer_whatsapp')->label('WhatsApp pembeli')->tel()->maxLength(30)
                            ->helperText('Opsional; dipakai tombol Buka WhatsApp.')
                            ->columnSpan(3),
                        Forms\Components\TextInput::make('payment_ref')->label('Referensi pembayaran')->maxLength(255)
                            ->helperText('Boleh menyusul, akan ditandai.')
                            ->columnSpan(3),
                        Forms\Components\TextInput::make('months')->label('Masa berlaku (bulan)')->numeric()->minValue(1)->maxValue(60)->required()->columnSpan(2),
                        Forms\Components\TextInput::make('amount_idr')->label('Harga (Rp)')->numeric()->minValue(0)->columnSpan(2),
                        Forms\Components\Toggle::make('create_learner')
                            ->label('Buat akun belajar')
                            ->live()
                            ->inline(false)
                            ->columnSpan(2),
                        // Shown right under the toggle that needs it, not hidden inside "Opsi lain".
                        Forms\Components\Select::make('courses')
                            ->label('Akses course')
                            ->multiple()
                            ->options(fn () => Course::query()->orderBy('title')->pluck('title', 'id'))
                            ->visible(fn ($get) => (bool) $get('create_learner'))
                            ->columnSpan(6),
                    ]),
                    Section::make('Opsi lain')
                        ->compact()
                        ->collapsed()
                        ->columns(2)
                        ->schema([
                            Forms\Components\TextInput::make('seats')->label('Jumlah perangkat')->numeric()->minValue(1)->maxValue(1000)->required(),
                            Forms\Components\TextInput::make('order_ref')->label('Referensi order')->required()->maxLength(100)
                                ->helperText('Unik. Mengulang referensi yang sama tidak pernah menerbitkan dua lisensi.'),
                            Forms\Components\Textarea::make('notes')->label('Catatan')->rows(2)->columnSpanFull(),
                            LicenseIssuanceResource::proofUpload()->label('Bukti pembayaran')->columnSpanFull(),
                        ]),
                ])
                ->action(function (array $data, Actions\Action $action): void {
                    // A free license (amount 0) is a gift: say why, and it goes to the review queue.
                    if (filled($data['amount_idr'] ?? null) && (int) $data['amount_idr'] === 0 && blank($data['notes'] ?? null)) {
                        Notification::make()->title('Harga 0: isi alasan di Catatan (Opsi lain)')->danger()->send();
                        $action->halt();

                        return;
                    }

                    try {
                        $row = app(IssueLicense::class)->handle(new IssueRequest(
                            email: $data['email'],
                            name: $data['name'],
                            months: (int) $data['months'],
                            orderRef: $data['order_ref'],
                            seats: filled($data['seats'] ?? null) ? (int) $data['seats'] : null,
                            amountIdr: filled($data['amount_idr'] ?? null) ? (int) $data['amount_idr'] : null,
                            buyerWhatsapp: $data['buyer_whatsapp'] ?? null,
                            notes: $data['notes'] ?? null,
                            issuedBy: auth()->id(),
                            paymentRef: $data['payment_ref'] ?? null,
                            paymentProofPath: $data['payment_proof'] ?? null,
                        ));
                    } catch (LicenseApiException|InvalidArgumentException $e) {
                        Notification::make()
                            ->title('Lisensi belum terbit')
                            ->body($e->getMessage().($e instanceof LicenseApiException && $e->isUnreachable() ? ' Baris tersimpan sebagai pending; gunakan Coba lagi.' : ''))
                            ->danger()
                            ->persistent()
                            ->send();
                        $action->halt();

                        return;
                    }

                    // The set-password link exists only in this request and in the modal opened next; it is never stored.
                    $setPasswordUrl = ! empty($data['create_learner']) ? $this->attachLearner($row, $data) : null;

                    Notification::make()->title('Lisensi terbit')->success()->send();

                    $this->replaceMountedTableAction('sendKey', (string) $row->getKey(), ['set_password_url' => $setPasswordUrl]);
                }),
        ];
    }

    /** The price a buyer would pay right now: promotion, then launch, then normal. */
    private function suggestedPrice(): int
    {
        return app(Offer::class)->currentPrice();
    }

    /**
     * Link the license to a learner account (created if the email has none) and grant the chosen courses.
     *
     * @return ?string the one-time set-password link when the account was created here, null for an existing account
     */
    private function attachLearner(LicenseIssuance $row, array $data): ?string
    {
        $courses = Course::query()->whereKey($data['courses'] ?? [])->get();
        $user = User::where('email', $data['email'])->first();
        $setPasswordUrl = null;

        if ($user === null) {
            // A random password nobody sees: the buyer chooses their own through the one-time link in the key message.
            $user = app(CreateLearner::class)->handle($data['name'], $data['email'], Str::random(64), $courses);
            $setPasswordUrl = app(IssueSetPasswordLink::class)->handle($user, IssueSetPasswordLink::WELCOME);

            Notification::make()->title('Akun learner dibuat')->body($user->email)->success()->send();
        } else {
            foreach ($courses as $course) {
                $user->courseAccess()->firstOrCreate(['course_id' => $course->id], ['granted_at' => now()]);
            }
        }

        $row->forceFill(['user_id' => $user->id])->save();

        return $setPasswordUrl;
    }
}
