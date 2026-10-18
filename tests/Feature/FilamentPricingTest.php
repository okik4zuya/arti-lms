<?php

namespace Tests\Feature;

use App\Filament\Pages\PricingSettings;
use App\Filament\Pages\SiteSettings;
use App\Filament\Resources\PromotionResource;
use App\Filament\Resources\PromotionResource\Pages\CreatePromotion;
use App\Filament\Resources\PromotionResource\Pages\ListPromotions;
use App\Models\Order;
use App\Models\Promotion;
use App\Models\SettingChange;
use App\Models\SiteSetting;
use App\Models\User;
use App\Support\Offer;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FilamentPricingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('permissions:sync')->assertSuccessful();
        Filament::setCurrentPanel('jamrud');
        config(['site.offer.launch_sold' => null, 'site.offer.price_normal' => 199000, 'site.offer.price_launch' => 99000, 'site.offer.launch_quota' => 100]);
    }

    private function as(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);
        $this->actingAs($user);

        return $user;
    }

    public function test_only_an_admin_may_open_prices_and_promotions(): void
    {
        $this->as('support');
        $this->assertFalse(PricingSettings::canAccess());
        $this->assertFalse(PromotionResource::canAccess());
        $this->get('/jamrud/pricing-settings')->assertForbidden();

        $this->as('admin');
        $this->assertTrue(PricingSettings::canAccess());
        $this->get('/jamrud/pricing-settings')->assertOk()->assertSee('Harga');
        $this->get('/jamrud/promotions')->assertOk();
    }

    public function test_saving_prices_changes_what_the_site_charges_and_is_audited(): void
    {
        $admin = $this->as('admin');

        Livewire::test(PricingSettings::class)
            ->assertFormSet(['price_normal' => 199000, 'price_launch' => 99000, 'launch_quota' => 100, 'launch_sold' => 0])
            ->fillForm(['price_normal' => 249000, 'price_launch' => 129000, 'launch_quota' => 150, 'launch_sold' => 5])
            ->callAction('save')
            ->assertNotified('Harga disimpan');

        $this->assertSame('249000', SiteSetting::values()['price_normal']);
        $offer = new Offer;
        $this->assertSame([249000, 129000, 150, 5], [$offer->normalPrice(), $offer->launchPrice(), $offer->quota(), $offer->manualSold()]);
        $this->assertSame(129000, $offer->currentPrice());

        $change = SettingChange::where('key', 'price_normal')->firstOrFail();
        $this->assertSame(['199000', '249000', $admin->id], [$change->old_value, $change->new_value, $change->user_id]);
        $this->assertSame(4, SettingChange::count());

        $this->get('/')->assertSee('Rp129.000')->assertSee('Rp249.000');
    }

    public function test_prices_survive_a_fresh_request_because_they_are_read_from_the_settings_table(): void
    {
        SiteSetting::put('price_normal', '300000');
        SiteSetting::put('price_launch', '150000');

        // A fresh boot of the provider applies the stored values over the config defaults (as on every request).
        \Illuminate\Support\Facades\Cache::forget(SiteSetting::CACHE_KEY);
        $this->app->getProvider(\App\Providers\AppServiceProvider::class)->boot();

        $this->assertSame(300000, (new Offer)->normalPrice());
        $this->assertSame(150000, (new Offer)->launchPrice());
    }

    public function test_bad_prices_are_refused_and_nothing_is_saved(): void
    {
        $this->as('admin');

        Livewire::test(PricingSettings::class)
            ->fillForm(['price_launch' => 250000])
            ->call('save')
            ->assertHasFormErrors(['price_launch']);
        Livewire::test(PricingSettings::class)
            ->fillForm(['price_normal' => 500])
            ->call('save')
            ->assertHasFormErrors(['price_normal']);
        Livewire::test(PricingSettings::class)
            ->fillForm(['launch_quota' => 0])
            ->call('save')
            ->assertHasFormErrors(['launch_quota']);

        $this->assertSame(0, SettingChange::count());
        $this->assertSame(199000, (new Offer)->normalPrice());
    }

    public function test_the_quota_cannot_drop_below_the_places_orders_already_hold(): void
    {
        $this->as('admin');
        for ($i = 1; $i <= 40; $i++) {
            Order::create(['public_id' => "arti-h{$i}", 'name' => 'X', 'email' => "h{$i}@example.test", 'amount' => 99000, 'price_tier' => 'launch', 'launch_slot' => $i, 'status' => Order::FULFILLED, 'gateway_ref' => "h{$i}"]);
        }

        Livewire::test(PricingSettings::class)
            ->fillForm(['launch_quota' => 30])
            ->callAction('save')
            ->assertNotified('Kuota terlalu kecil');

        $this->assertSame(100, (new Offer)->quota());
        $this->assertSame(0, SettingChange::count());
    }

    public function test_the_site_settings_page_does_not_touch_the_prices(): void
    {
        $this->as('admin');
        SiteSetting::put('price_normal', '300000');

        Livewire::test(SiteSettings::class)->call('save');

        $this->assertSame('300000', SiteSetting::values()['price_normal']);
    }

    public function test_an_admin_creates_a_promotion_and_the_site_shows_it_with_its_end_date(): void
    {
        $this->as('admin');

        Livewire::test(CreatePromotion::class)
            ->fillForm([
                'name' => 'hari-peneliti', 'label' => 'Promo Hari Peneliti', 'price' => 79000, 'quota' => null, 'active' => true,
                'starts_at' => now()->subHour()->toDateTimeString(), 'ends_at' => now()->addDays(2)->setTime(23, 59)->toDateTimeString(),
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $promo = Promotion::firstOrFail();
        $this->assertSame(79000, (new Offer)->currentPrice());
        $this->assertSame('promo', (new Offer)->tier());
        $this->assertTrue(SettingChange::where('key', 'promotion:hari-peneliti')->exists());

        $this->get('/')->assertSee('Promo Hari Peneliti')->assertSee('Rp79.000')->assertSee('Promo sampai')->assertSee('23.59');
    }

    public function test_promotion_rules_price_below_normal_end_after_start_and_unique_name(): void
    {
        $this->as('admin');
        Promotion::create(['name' => 'dupe', 'label' => 'x', 'price' => 50000, 'starts_at' => now(), 'ends_at' => now()->addDay(), 'active' => true]);
        $base = ['name' => 'baru', 'label' => 'Promo', 'price' => 79000, 'active' => true, 'starts_at' => now()->toDateTimeString(), 'ends_at' => now()->addDay()->toDateTimeString()];

        $cases = [
            [['price' => 199000], 'price'],
            [['price' => 5000], 'price'],
            [['ends_at' => now()->subDay()->toDateTimeString()], 'ends_at'],
            [['name' => 'dupe'], 'name'],
            [['label' => ''], 'label'],
        ];

        foreach ($cases as [$override, $field]) {
            Livewire::test(CreatePromotion::class)->fillForm($override + $base)->call('create')->assertHasFormErrors([$field]);
        }

        $this->assertSame(1, Promotion::count());
    }

    public function test_changes_and_deletion_of_a_promotion_are_audited_and_a_promotion_with_orders_cannot_be_deleted(): void
    {
        $admin = $this->as('admin');
        $promo = Promotion::create(['name' => 'event', 'label' => 'Promo', 'price' => 79000, 'starts_at' => now()->subHour(), 'ends_at' => now()->addDay(), 'active' => true]);

        $promo->update(['price' => 69000]);
        $change = SettingChange::where('key', 'promotion:event')->latest('created_at')->get()->first(fn ($c) => str_contains((string) $c->new_value, '69000'));
        $this->assertNotNull($change);
        $this->assertSame($admin->id, $change->user_id);

        $order = Order::create(['public_id' => 'arti-p1', 'name' => 'X', 'email' => 'x@example.test', 'amount' => 69000, 'price_tier' => 'promo', 'promotion_id' => $promo->id, 'status' => Order::PENDING]);
        $this->assertFalse(PromotionResource::canDelete($promo));

        $order->delete();
        $this->assertTrue(PromotionResource::canDelete($promo->fresh()));
        $promo->delete();
        $this->assertTrue(SettingChange::where('key', 'promotion:event')->where('new_value', 'deleted')->exists());
    }

    public function test_the_list_shows_the_state_of_each_promotion(): void
    {
        $this->as('admin');
        $running = Promotion::create(['name' => 'a', 'label' => 'Berjalan A', 'price' => 79000, 'starts_at' => now()->subHour(), 'ends_at' => now()->addDay(), 'active' => true]);
        $later = Promotion::create(['name' => 'b', 'label' => 'Nanti B', 'price' => 79000, 'starts_at' => now()->addDay(), 'ends_at' => now()->addDays(2), 'active' => true]);
        $off = Promotion::create(['name' => 'c', 'label' => 'Mati C', 'price' => 79000, 'starts_at' => now()->subHour(), 'ends_at' => now()->addDay(), 'active' => false]);

        $this->assertSame('Berjalan', PromotionResource::state($running));
        $this->assertSame('Terjadwal', PromotionResource::state($later));
        $this->assertSame('Nonaktif', PromotionResource::state($off));

        Livewire::test(ListPromotions::class)->assertCanSeeTableRecords([$running, $later, $off])->assertSee('Terjadwal');
    }

    public function test_a_promotion_that_ended_or_is_off_does_not_change_the_price(): void
    {
        Promotion::create(['name' => 'over', 'label' => 'Selesai', 'price' => 10000, 'starts_at' => now()->subDays(3), 'ends_at' => now()->subDay(), 'active' => true]);

        $this->assertSame(99000, (new Offer)->currentPrice());
        $this->get('/')->assertDontSee('Selesai');
    }
}
