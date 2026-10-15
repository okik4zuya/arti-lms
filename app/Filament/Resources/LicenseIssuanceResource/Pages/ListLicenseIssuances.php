<?php

namespace App\Filament\Resources\LicenseIssuanceResource\Pages;

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
                ->modalHeading('Terbitkan lisensi baru')
                ->modalSubmitActionLabel('Terbitkan')
                ->fillForm(fn () => [
                    'tier' => 'individual',
                    'months' => 12,
                    'amount_idr' => $this->suggestedPrice(),
                    'order_ref' => 'WA-'.now()->format('ymd').'-'.Str::upper(Str::random(4)),
                    'password' => Str::password(12),
                ])
                ->schema([
                    Forms\Components\TextInput::make('email')->email()->required()->maxLength(255),
                    Forms\Components\TextInput::make('name')->label('Nama')->required()->maxLength(255),
                    Forms\Components\Select::make('tier')
                        ->options(['individual' => 'individual', 'lab' => 'lab', 'academic' => 'academic', 'site' => 'site'])
                        ->required(),
                    Forms\Components\TextInput::make('months')->label('Masa berlaku (bulan)')->numeric()->minValue(1)->maxValue(60)->required(),
                    Forms\Components\TextInput::make('seats')->label('Jumlah perangkat')->numeric()->minValue(1)->helperText('Kosongkan untuk bawaan tier.'),
                    Forms\Components\TextInput::make('amount_idr')->label('Harga (Rp)')->numeric()->minValue(0),
                    Forms\Components\TextInput::make('buyer_whatsapp')->label('WhatsApp pembeli')->tel()->maxLength(30)
                        ->helperText('Opsional; dipakai tombol Buka WhatsApp.'),
                    Forms\Components\TextInput::make('order_ref')->label('Referensi order')->required()->maxLength(100)
                        ->helperText('Unik. Mengulang referensi yang sama tidak pernah menerbitkan dua lisensi.'),
                    Forms\Components\Textarea::make('notes')->label('Catatan')->rows(2),
                    Forms\Components\Toggle::make('create_learner')
                        ->label('Buat akun learner dan beri akses course')
                        ->live(),
                    Forms\Components\Select::make('courses')
                        ->label('Course')
                        ->multiple()
                        ->options(fn () => Course::query()->orderBy('title')->pluck('title', 'id'))
                        ->visible(fn ($get) => (bool) $get('create_learner')),
                    Forms\Components\TextInput::make('password')
                        ->label('Password sementara')
                        ->password()
                        ->revealable()
                        ->minLength(8)
                        ->visible(fn ($get) => (bool) $get('create_learner'))
                        ->requiredIf('create_learner', true)
                        ->helperText('Hanya dipakai bila email belum punya akun.'),
                ])
                ->action(function (array $data, Actions\Action $action): void {
                    try {
                        $row = app(IssueLicense::class)->handle(new IssueRequest(
                            email: $data['email'],
                            name: $data['name'],
                            tier: $data['tier'],
                            months: (int) $data['months'],
                            orderRef: $data['order_ref'],
                            seats: filled($data['seats'] ?? null) ? (int) $data['seats'] : null,
                            amountIdr: filled($data['amount_idr'] ?? null) ? (int) $data['amount_idr'] : null,
                            buyerWhatsapp: $data['buyer_whatsapp'] ?? null,
                            notes: $data['notes'] ?? null,
                            issuedBy: auth()->id(),
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

                    if (! empty($data['create_learner'])) {
                        $this->attachLearner($row, $data);
                    }

                    Notification::make()->title('Lisensi terbit')->success()->send();

                    $this->replaceMountedTableAction('sendKey', (string) $row->getKey());
                }),
        ];
    }

    /** Launch price while buyers are below the quota, then the normal price. */
    private function suggestedPrice(): int
    {
        $offer = app(Offer::class);

        return LicenseIssuance::sold()->count() < $offer->quota() ? $offer->launchPrice() : $offer->normalPrice();
    }

    /** Link the license to a learner account (created if the email has none) and grant the chosen courses. */
    private function attachLearner(LicenseIssuance $row, array $data): void
    {
        $courses = Course::query()->whereKey($data['courses'] ?? [])->get();
        $user = User::where('email', $data['email'])->first();

        if ($user === null) {
            $user = app(CreateLearner::class)->handle($data['name'], $data['email'], $data['password'], $courses);

            Notification::make()
                ->title('Akun learner dibuat')
                ->body("{$user->email}, password sementara: {$data['password']}")
                ->persistent()
                ->success()
                ->send();
        } else {
            foreach ($courses as $course) {
                $user->courseAccess()->firstOrCreate(['course_id' => $course->id], ['granted_at' => now()]);
            }
        }

        $row->forceFill(['user_id' => $user->id])->save();
    }
}
