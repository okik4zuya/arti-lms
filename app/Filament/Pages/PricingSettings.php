<?php

namespace App\Filament\Pages;

use App\Models\SettingChange;
use App\Models\SiteSetting;
use App\Support\Offer;
use App\Support\Quota;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

/**
 * Prices and the launch batch, editable without a deploy. A saved value overrides config/site.php (see
 * AppServiceProvider); every change is written to setting_changes. Orders keep the price they were created with,
 * so a change only affects purchases started after it. Event promotions live in the "Promo" list.
 */
class PricingSettings extends Page
{
    protected static ?string $title = 'Harga & promo';

    protected static ?string $navigationLabel = 'Harga';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 6;

    protected string $view = 'filament.pages.pricing-settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->can('site.manage') ?? false;
    }

    public function mount(): void
    {
        $this->form->fill([
            'price_normal' => (int) config('site.offer.price_normal'),
            'price_launch' => (int) config('site.offer.price_launch'),
            'launch_quota' => (int) config('site.offer.launch_quota'),
            'launch_sold' => (new Offer)->manualSold(),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Placeholder::make('now')
                    ->label('Yang berlaku sekarang')
                    ->content(function () {
                        $offer = new Offer;

                        return $offer->format($offer->currentPrice()).' ('.['promo' => 'promo', 'launch' => 'harga perintis', 'normal' => 'harga normal'][$offer->tier()].'). '
                            .'Tempat perintis terpakai '.$offer->sold().' dari '.$offer->quota().'.';
                    }),
                TextInput::make('price_normal')
                    ->label('Harga normal (Rp)')
                    ->numeric()->integer()->required()->minValue(10000)->maxValue(5000000)
                    ->helperText('Harga sesudah kuota perintis habis dan di luar promo. Bayar sekali.'),
                TextInput::make('price_launch')
                    ->label('Harga perintis (Rp)')
                    ->numeric()->integer()->required()->minValue(10000)->lte('price_normal')
                    ->helperText('Harga untuk pembeli pertama sampai kuota habis. Tidak boleh lebih tinggi dari harga normal.'),
                TextInput::make('launch_quota')
                    ->label('Kuota perintis (jumlah pembeli)')
                    ->numeric()->integer()->required()->minValue(1)->maxValue(1000)
                    ->helperText('Tidak bisa diturunkan di bawah tempat yang sudah terpakai pesanan.'),
                TextInput::make('launch_sold')
                    ->label('Terjual di luar situs (manual)')
                    ->numeric()->integer()->required()->minValue(0)
                    ->helperText('Penjualan lewat WhatsApp sebelum pembelian otomatis. Dihitung ke kuota perintis bersama pesanan situs.'),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan harga')
                ->requiresConfirmation()
                ->modalHeading('Simpan harga baru?')
                ->modalDescription('Harga di halaman utama, email, dan checkout langsung berubah untuk pembelian berikutnya. Pesanan yang sudah dibuat tidak berubah.')
                ->action(fn () => $this->save()),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $held = Quota::launchHeld();
        if ((int) $data['launch_quota'] - (int) $data['launch_sold'] < $held) {
            Notification::make()->title('Kuota terlalu kecil')
                ->body("Sudah ada {$held} tempat perintis terpakai pesanan situs. Kuota dikurangi hitungan manual harus minimal {$held}.")
                ->danger()->send();

            return;
        }

        $current = [
            'price_normal' => (int) config('site.offer.price_normal'),
            'price_launch' => (int) config('site.offer.price_launch'),
            'launch_quota' => (int) config('site.offer.launch_quota'),
            'launch_sold' => (new Offer)->manualSold(),
        ];

        foreach (array_keys(SiteSetting::PRICING_KEYS) as $key) {
            $new = (int) $data[$key];

            if ($new !== $current[$key]) {
                SettingChange::record($key, $current[$key], $new);
            }

            SiteSetting::put($key, (string) $new);
            config([SiteSetting::PRICING_KEYS[$key] => $new]);
        }

        Notification::make()->title('Harga disimpan')->success()->send();
    }
}
