<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class SiteSettings extends Page
{
    protected static ?string $title = 'Pengaturan situs';

    protected static ?string $navigationLabel = 'Pengaturan situs';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected string $view = 'filament.pages.site-settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->can('site.manage') ?? false;
    }

    public function mount(): void
    {
        $values = SiteSetting::values();

        $this->form->fill(array_merge(
            array_fill_keys(array_keys(SiteSetting::CONFIG_KEYS), null),
            array_intersect_key($values, SiteSetting::CONFIG_KEYS),
        ));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('contact_whatsapp')
                    ->label('Nomor WhatsApp')
                    ->placeholder('081234567890')
                    ->tel()
                    ->maxLength(20)
                    ->regex('/^\+?[0-9][0-9 \-]{7,18}$/')
                    ->validationMessages(['regex' => 'Isi nomor saja, contoh 081234567890 atau +6281234567890.'])
                    ->helperText('Dipakai tombol "Beli lewat WhatsApp", tombol WhatsApp mengambang, dan formulirnya. Awalan 0 diubah otomatis menjadi 62. Kosongkan untuk memakai nilai SITE_CONTACT_WHATSAPP di .env.'),
                Textarea::make('whatsapp_message')
                    ->label('Pesan default pembelian')
                    ->rows(2)
                    ->maxLength(300)
                    ->placeholder(config('site.offer.whatsapp_message'))
                    ->helperText('Terisi otomatis di formulir saat pengunjung menekan "Beli lewat WhatsApp", dan menjadi pesan di tautan WhatsApp tombol itu bila JavaScript mati. Di formulir, sapaan "Halo," di awal dibuang karena formulir sudah membuka dengan "Halo". Kosongkan untuk memakai teks bawaan.'),
                Textarea::make('chat_message')
                    ->label('Pesan default chat umum')
                    ->rows(2)
                    ->maxLength(300)
                    ->placeholder(config('site.offer.chat_message'))
                    ->helperText('Pesan di tautan tombol WhatsApp mengambang bila JavaScript mati. Formulirnya sendiri dimulai kosong untuk keperluan selain pembelian. Kosongkan untuk memakai teks bawaan.'),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach (array_keys(SiteSetting::CONFIG_KEYS) as $key) {
            SiteSetting::put($key, filled($data[$key] ?? null) ? trim($data[$key]) : null);
        }

        Notification::make()->title('Pengaturan disimpan')->success()->send();
    }
}
