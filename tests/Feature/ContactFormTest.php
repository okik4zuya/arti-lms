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

    protected function payload(array $override = []): array
    {
        return array_merge(['nama' => 'Budi', 'topik' => 'instalasi', 'pesan' => 'Instalasi berhenti di langkah 2.'], $override);
    }

    public function test_no_form_while_no_number_is_set(): void
    {
        config(['site.contact_whatsapp' => null]);

        $this->get('/kontak')->assertOk()->assertSee('belum dibuka')->assertDontSee('Kirim lewat WhatsApp');
        $this->post('/kontak', $this->payload())->assertNotFound();
    }

    public function test_form_redirects_to_whatsapp_with_the_message(): void
    {
        config(['site.contact_whatsapp' => '0812-3456-7890']);

        $this->get('/kontak')->assertOk()->assertSee('Kirim lewat WhatsApp')->assertSee('Bantuan instalasi');

        $response = $this->post('/kontak', $this->payload());
        $location = $response->headers->get('Location');

        $this->assertStringStartsWith('https://wa.me/6281234567890?text=', $location);
        $text = urldecode(substr($location, strlen('https://wa.me/6281234567890?text=')));
        $this->assertStringContainsString('Halo, saya Budi.', $text);
        $this->assertStringContainsString('Keperluan: Bantuan instalasi', $text);
        $this->assertStringContainsString('Instalasi berhenti di langkah 2.', $text);
    }

    public function test_form_validates_input(): void
    {
        config(['site.contact_whatsapp' => '081234567890']);

        $this->from('/kontak')->post('/kontak', $this->payload(['nama' => '', 'topik' => 'x', 'pesan' => '']))
            ->assertRedirect('/kontak')
            ->assertSessionHasErrors(['nama', 'topik', 'pesan']);
    }

    public function test_a_number_saved_in_the_admin_overrides_the_env_default(): void
    {
        config(['site.contact_whatsapp' => '081111111111']);

        SiteSetting::put('contact_whatsapp', '+62 899-0000-1111');
        (new \App\Providers\AppServiceProvider($this->app))->boot();

        $this->assertSame('+62 899-0000-1111', config('site.contact_whatsapp'));
        $this->get('/kontak')->assertSee('Kirim lewat WhatsApp');
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

    public function test_floating_whatsapp_button_shows_only_with_a_number_and_not_on_the_contact_page(): void
    {
        config(['site.contact_whatsapp' => null]);
        $this->get('/')->assertDontSee('wa-float');

        config(['site.contact_whatsapp' => '081234567890']);
        $this->get('/')->assertSee('class="wa-float"', false)->assertSee('https://wa.me/6281234567890?text=', false)->assertSee('aria-label="Chat lewat WhatsApp"', false);
        $this->get('/fitur')->assertSee('wa-float');
        $this->get('/kontak')->assertDontSee('wa-float');
    }
}
