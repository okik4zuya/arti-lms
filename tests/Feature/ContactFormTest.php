<?php

namespace Tests\Feature;

use App\Filament\Pages\SiteSettings;
use App\Models\SiteSetting;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_there_is_no_floating_whatsapp_button_or_panel_anywhere(): void
    {
        config(['site.contact_whatsapp' => '081234567890']);

        foreach (['/', '/fitur', '/kontak', '/scipeek', '/artipdf', '/baca/01-2-masalah-utama'] as $path) {
            $html = $this->get($path)->assertOk()->getContent();

            $this->assertStringNotContainsString('wa-float', $html, $path);
            $this->assertStringNotContainsString('wa-panel', $html, $path);
            $this->assertStringNotContainsString('data-kontak', $html, $path);
            $this->assertStringNotContainsString('wa.me', $html, $path);
            $this->assertStringNotContainsString('contact-panel', $html, $path);
        }
    }

    public function test_the_footer_shows_the_support_email_on_every_page(): void
    {
        foreach (['/', '/fitur', '/kontak', '/privasi'] as $path) {
            $this->get($path)->assertSee('href="mailto:hi@arti.ai.id"', false);
        }

        config(['site.contact_email' => 'bantuan@example.test']);
        $this->get('/')->assertSee('href="mailto:bantuan@example.test"', false)->assertDontSee('hi@arti.ai.id');
    }

    public function test_the_contact_page_is_email_only_and_the_old_form_is_gone(): void
    {
        $this->get('/kontak')->assertOk()->assertSee('hi@arti.ai.id')->assertDontSee('Kirim lewat WhatsApp')->assertDontSee('<form', false);
        $this->post('/kontak', ['topik' => 'instalasi', 'pesan' => 'x'])->assertStatus(405);
    }

    public function test_admin_can_save_default_messages_and_blank_falls_back_to_the_config(): void
    {
        Role::firstOrCreate(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);
        Filament::setCurrentPanel('jamrud');

        Livewire::test(SiteSettings::class)
            ->fillForm(['whatsapp_message' => 'Pesan beli baru', 'chat_message' => ''])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Pesan beli baru', SiteSetting::find('whatsapp_message')->value);
        $this->assertNull(SiteSetting::find('chat_message')->value);

        Livewire::test(SiteSettings::class)
            ->fillForm(['whatsapp_message' => str_repeat('a', 301)])
            ->call('save')
            ->assertHasFormErrors(['whatsapp_message']);
    }

    public function test_admin_can_edit_the_number_and_a_learner_cannot_open_the_page(): void
    {
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'learner']);
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $learner = User::factory()->create();
        $learner->assignRole('learner');

        $this->actingAs($learner)->get('/jamrud/site-settings')->assertForbidden();

        $this->actingAs($admin);
        Filament::setCurrentPanel('jamrud');

        $this->get('/jamrud/site-settings')->assertOk()->assertSee('Nomor WhatsApp');

        Livewire::test(SiteSettings::class)
            ->fillForm(['contact_whatsapp' => 'abc'])
            ->call('save')
            ->assertHasFormErrors(['contact_whatsapp']);

        Livewire::test(SiteSettings::class)
            ->fillForm(['contact_whatsapp' => '081234567890'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('081234567890', SiteSetting::find('contact_whatsapp')->value);
    }
}
