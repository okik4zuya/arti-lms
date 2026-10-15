<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use BackedEnum;
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

    public function mount(): void
    {
        $this->form->fill([
            'contact_whatsapp' => SiteSetting::values()['contact_whatsapp'] ?? null,
        ]);
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
                    ->helperText('Dipakai tombol "Beli lewat WhatsApp" dan formulir di /kontak. Awalan 0 diubah otomatis menjadi 62. Kosongkan untuk memakai nilai SITE_CONTACT_WHATSAPP di .env.'),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        SiteSetting::put('contact_whatsapp', filled($data['contact_whatsapp'] ?? null) ? trim($data['contact_whatsapp']) : null);

        Notification::make()->title('Pengaturan disimpan')->success()->send();
    }
}
