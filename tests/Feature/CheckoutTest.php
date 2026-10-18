<?php

namespace Tests\Feature;

use App\Actions\Orders\CreateCheckout;
use App\Models\LicenseIssuance;
use App\Models\Order;
use App\Models\Promotion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.payments.driver' => 'xendit',
            'services.xendit.url' => 'https://xendit.test',
            'services.xendit.secret_key' => 'xnd_development_test',
            'services.xendit.callback_token' => 'cb-token',
            'site.contact_whatsapp' => '081234567890',
            'site.offer.launch_sold' => null,
        ]);
        Queue::fake();
        $this->xendit();
    }

    private function xendit(int $status = 200): void
    {
        Http::swap(new Factory);
        Http::fake(['xendit.test/v2/invoices' => fn (Request $r) => Http::response(
            $status === 200
                ? ['id' => 'inv-'.$r['external_id'], 'invoice_url' => 'https://checkout.xendit.test/'.$r['external_id'], 'status' => 'PENDING']
                : ['error_code' => 'SERVER_ERROR'],
            $status,
        )]);
    }

    private function buy(array $over = [], string $ip = '10.1.1.1')
    {
        return $this->withServerVariables(['REMOTE_ADDR' => $ip])->post('/beli', $over + [
            'nama' => 'Budi', 'email' => 'budi@example.test', 'whatsapp' => '081234567890', 'setuju' => '1',
        ]);
    }

    private function holdLaunchSlots(int $count): void
    {
        for ($i = 1; $i <= $count; $i++) {
            Order::create([
                'public_id' => "arti-held-{$i}", 'name' => 'X', 'email' => "held{$i}@example.test", 'amount' => 99000, 'price_tier' => 'launch',
                'launch_slot' => $i, 'status' => Order::FULFILLED, 'gateway_ref' => "held-{$i}",
            ]);
        }
    }

    public function test_without_a_gateway_the_buy_page_sends_the_visitor_to_whatsapp(): void
    {
        config(['services.xendit.secret_key' => '']);

        $this->get('/beli')->assertRedirectContains('https://wa.me/6281234567890');
    }

    public function test_the_buy_page_shows_the_launch_price_the_remaining_places_and_the_whatsapp_fallback(): void
    {
        $this->holdLaunchSlots(3);

        $this->get('/beli')->assertOk()
            ->assertSee('Harga perintis')
            ->assertSee('Rp99.000')
            ->assertSee('tersisa 97 dari 100')
            ->assertSee('Beli lewat WhatsApp')
            ->assertSee('noindex', false);
    }

    public function test_a_purchase_reserves_the_price_and_a_place_and_sends_the_buyer_to_the_payment_page(): void
    {
        $response = $this->buy(['email' => 'Budi@Example.test']);

        $order = Order::firstOrFail();
        $response->assertRedirect('https://checkout.xendit.test/'.$order->public_id);
        $this->assertSame('budi@example.test', $order->email);
        $this->assertSame(99000, $order->amount);
        $this->assertSame('launch', $order->price_tier);
        $this->assertSame(1, $order->launch_slot);
        $this->assertSame(Order::PENDING, $order->status);
        $this->assertSame('inv-'.$order->public_id, $order->gateway_ref);
        $this->assertSame('6281234567890', $order->whatsapp);
        $this->assertEqualsWithDelta(now()->addMinutes(60)->timestamp, $order->expires_at->timestamp, 5);

        Http::assertSent(fn (Request $r) => $r['amount'] === 99000 && $r['external_id'] === $order->public_id
            && $r['invoice_duration'] === 3600 && str_contains($r['success_redirect_url'], '/beli/selesai/'.$order->public_id)
            && str_contains($r['failure_redirect_url'], 'gagal=1') && $r['payer_email'] === 'budi@example.test');
    }

    public function test_the_form_is_validated_and_a_filled_honeypot_creates_nothing(): void
    {
        $this->buy(['nama' => ''])->assertSessionHasErrors('nama');
        $this->buy(['email' => 'bukan-email'])->assertSessionHasErrors('email');
        $this->buy(['setuju' => null])->assertSessionHasErrors('setuju');
        $this->buy(['whatsapp' => 'abc'])->assertSessionHasErrors('whatsapp');
        $this->buy(['website' => 'http://spam.example'])->assertSessionHasErrors('website');

        $this->assertSame(0, Order::count());
        Http::assertNothingSent();
    }

    public function test_asking_twice_with_the_same_email_reuses_the_open_order_and_one_place(): void
    {
        $first = $this->buy();
        $second = $this->buy(['nama' => 'Budi Lagi']);

        $this->assertSame(1, Order::count());
        $first->assertRedirect($second->headers->get('Location'));
        Http::assertSentCount(1);
    }

    public function test_an_email_with_an_active_license_cannot_buy_again(): void
    {
        LicenseIssuance::create(['order_ref' => 'WA-1', 'source' => 'manual', 'email' => 'budi@example.test', 'name' => 'Budi', 'tier' => 'individual', 'months' => 12])
            ->forceFill(['status' => LicenseIssuance::ACTIVE])->save();

        $this->buy()->assertSessionHasErrors('email');

        $this->assertSame(0, Order::count());
        Http::assertNothingSent();
    }

    public function test_when_the_gateway_fails_the_place_is_given_back_and_the_buyer_is_pointed_to_whatsapp(): void
    {
        $this->xendit(500);

        $this->buy()->assertSessionHasErrors('pembayaran');

        $order = Order::firstOrFail();
        $this->assertSame(Order::FAILED, $order->status);
        $this->assertNull($order->launch_slot);

        $this->xendit();
        $this->buy();
        $this->assertSame(1, Order::where('status', Order::PENDING)->firstOrFail()->launch_slot);
    }

    public function test_the_last_launch_place_is_taken_once_and_the_next_buyer_pays_the_normal_price(): void
    {
        $this->holdLaunchSlots(99);

        $this->buy(['email' => 'last@example.test']);
        $this->buy(['email' => 'after@example.test']);

        $last = Order::where('email', 'last@example.test')->firstOrFail();
        $after = Order::where('email', 'after@example.test')->firstOrFail();
        $this->assertSame([100, 99000, 'launch'], [$last->launch_slot, $last->amount, $last->price_tier]);
        $this->assertSame([null, 199000, 'normal'], [$after->launch_slot, $after->amount, $after->price_tier]);
        $this->assertSame(100, Order::whereNotNull('launch_slot')->count());
    }

    public function test_sales_made_outside_the_gateway_use_up_places(): void
    {
        config(['site.offer.launch_sold' => 98]);

        $this->buy(['email' => 'a@example.test']);
        $this->buy(['email' => 'b@example.test']);
        $this->buy(['email' => 'c@example.test']);

        $this->assertSame([99000, 99000, 199000], Order::orderBy('created_at')->orderBy('email')->pluck('amount')->all());
        $this->assertSame([1, 2], Order::whereNotNull('launch_slot')->orderBy('launch_slot')->pluck('launch_slot')->all());
    }

    public function test_two_payments_racing_for_one_place_never_share_it(): void
    {
        $this->holdLaunchSlots(98);
        $raced = false;
        // Just before the first INSERT another buyer takes slot 99: the unique index must make us count again.
        $this->rivalTakesSlot($raced, 99);

        $order = app(CreateCheckout::class)->handle('Me', 'me@example.test', null);

        $this->assertSame(100, $order->launch_slot);
        $this->assertSame(99000, $order->amount);
        $slots = Order::whereNotNull('launch_slot')->pluck('launch_slot');
        $this->assertSame($slots->count(), $slots->unique()->count());
        $this->assertSame(100, $slots->count());
    }

    public function test_a_second_race_for_the_final_place_ends_on_the_normal_price(): void
    {
        $this->holdLaunchSlots(99);
        $raced = false;
        $this->rivalTakesSlot($raced, 100);

        $order = app(CreateCheckout::class)->handle('Me', 'me@example.test', null);

        $this->assertSame([null, 199000, 'normal'], [$order->launch_slot, $order->amount, $order->price_tier]);
        $this->assertSame(100, Order::whereNotNull('launch_slot')->count());
    }

    public function test_a_lapsed_payment_window_gives_its_place_back(): void
    {
        $this->buy(['email' => 'slow@example.test']);
        $this->travel(61)->minutes();

        $this->buy(['email' => 'quick@example.test']);

        $slow = Order::where('email', 'slow@example.test')->firstOrFail();
        $this->assertSame(Order::EXPIRED, $slow->status);
        $this->assertNull($slow->launch_slot);
        $this->assertSame(1, Order::where('email', 'quick@example.test')->firstOrFail()->launch_slot);
    }

    public function test_a_running_promotion_beats_the_launch_price_and_keeps_the_launch_places(): void
    {
        $promo = Promotion::create(['name' => 'event', 'label' => 'Promo Hari Peneliti', 'price' => 79000, 'starts_at' => now()->subDay(), 'ends_at' => now()->addDays(3), 'active' => true]);

        $this->get('/beli')->assertSee('Promo Hari Peneliti')->assertSee('Rp79.000')->assertDontSee('Harga perintis');
        $this->buy();

        $order = Order::firstOrFail();
        $this->assertSame([79000, 'promo', $promo->id, null], [$order->amount, $order->price_tier, $order->promotion_id, $order->launch_slot]);
    }

    public function test_a_promotion_with_a_quota_hands_out_its_own_places_then_the_launch_price_returns(): void
    {
        $promo = Promotion::create(['name' => 'event', 'label' => 'Promo', 'price' => 79000, 'quota' => 1, 'starts_at' => now()->subDay(), 'ends_at' => now()->addDay(), 'active' => true]);

        $this->buy(['email' => 'a@example.test']);
        $this->buy(['email' => 'b@example.test']);

        $a = Order::where('email', 'a@example.test')->firstOrFail();
        $b = Order::where('email', 'b@example.test')->firstOrFail();
        $this->assertSame([79000, 1, $promo->id], [$a->amount, $a->promo_slot, $a->promotion_id]);
        $this->assertSame([99000, 'launch', 1], [$b->amount, $b->price_tier, $b->launch_slot]);
    }

    public function test_promotions_that_are_off_future_or_over_are_ignored(): void
    {
        Promotion::create(['name' => 'off', 'label' => 'a', 'price' => 10000, 'starts_at' => now()->subDay(), 'ends_at' => now()->addDay(), 'active' => false]);
        Promotion::create(['name' => 'later', 'label' => 'b', 'price' => 10000, 'starts_at' => now()->addDay(), 'ends_at' => now()->addDays(2), 'active' => true]);
        Promotion::create(['name' => 'over', 'label' => 'c', 'price' => 10000, 'starts_at' => now()->subDays(2), 'ends_at' => now()->subDay(), 'active' => true]);

        $this->buy();

        $this->assertSame(99000, Order::firstOrFail()->amount);
    }

    public function test_the_status_page_shows_only_the_state_and_a_masked_email(): void
    {
        $this->buy(['email' => 'budi@example.test']);
        $order = Order::firstOrFail();

        $this->get("/beli/selesai/{$order->public_id}")->assertOk()->assertSee('Menunggu pembayaran')->assertSee('Lanjutkan pembayaran')->assertDontSee('budi@example.test')
            ->assertSee('http-equiv="refresh"', false);

        $order->forceFill(['status' => Order::FULFILLED])->save();
        $this->get("/beli/selesai/{$order->public_id}")->assertOk()->assertSee('Cek email Anda')->assertSee('b***@example.test')
            ->assertDontSee('ARTI-')->assertDontSee('atur-sandi')->assertDontSee('http-equiv="refresh"', false);

        $order->forceFill(['status' => Order::EXPIRED])->save();
        $this->get("/beli/selesai/{$order->public_id}")->assertSee('sudah ditutup')->assertSee('Beli lagi')->assertDontSee('http-equiv="refresh"', false);

        $this->get('/beli/selesai/arti-tidak-ada')->assertNotFound();
    }

    public function test_starting_orders_is_rate_limited_and_the_status_page_has_its_own_bucket(): void
    {
        for ($i = 1; $i <= 6; $i++) {
            $this->buy(['email' => "u{$i}@example.test"], '10.7.7.7')->assertRedirect();
        }
        $this->buy(['email' => 'u7@example.test'], '10.7.7.7')->assertStatus(429);

        $order = Order::firstOrFail();
        $this->withServerVariables(['REMOTE_ADDR' => '10.7.7.7'])->get("/beli/selesai/{$order->public_id}")->assertOk();
    }

    public function test_an_expiry_callback_gives_the_place_back_and_a_late_payment_takes_one_again(): void
    {
        $this->buy();
        $order = Order::firstOrFail();
        $this->invoiceCallback($order, 'EXPIRED')->assertOk();

        $order->refresh();
        $this->assertSame([Order::EXPIRED, null], [$order->status, $order->launch_slot]);

        $this->invoiceCallback($order, 'PAID', 99000)->assertOk();
        $order->refresh();
        $this->assertSame([Order::PAID, 1], [$order->status, $order->launch_slot]);
    }

    public function test_a_late_payment_with_no_place_left_waits_for_an_admin(): void
    {
        $this->buy();
        $order = Order::firstOrFail();
        $this->invoiceCallback($order, 'EXPIRED')->assertOk();
        $this->holdLaunchSlots(100);

        $this->invoiceCallback($order, 'PAID', 99000)->assertOk();

        $order->refresh();
        $this->assertSame(Order::NEEDS_ATTENTION, $order->status);
        $this->assertSame('late_payment_no_place', $order->attention_note);
    }

    /**
     * Another buyer takes `$slot` between our count and our INSERT. The failed INSERT rolls back its savepoint,
     * rival included, so the rival is inserted again when the retry opens its transaction (a real rival is a
     * separate connection and is never rolled back).
     */
    private function rivalTakesSlot(bool &$raced, int $slot): void
    {
        $rival = fn () => Order::insert([
            'id' => (string) \Illuminate\Support\Str::uuid(), 'public_id' => 'arti-rival', 'name' => 'Rival', 'email' => 'rival@example.test',
            'amount' => 99000, 'price_tier' => 'launch', 'launch_slot' => $slot, 'status' => 'pending', 'gateway' => 'xendit',
            'expires_at' => now()->addHour(), 'created_at' => now(), 'updated_at' => now(),
        ]);

        Order::creating(function (Order $order) use (&$raced, $rival) {
            if ($raced || $order->email !== 'me@example.test') {
                return;
            }
            $raced = true;
            $rival();
            $done = false;
            \Illuminate\Support\Facades\DB::beforeStartingTransaction(function () use (&$done, $rival) {
                if (! $done) {
                    $done = true;
                    $rival();
                }
            });
        });
    }

    private function invoiceCallback(Order $order, string $status, int $paid = 0)
    {
        Http::swap(new Factory);
        Http::fake(['xendit.test/v2/invoices/*' => Http::response([
            'id' => $order->gateway_ref, 'external_id' => $order->public_id, 'status' => $status, 'amount' => $order->amount, 'paid_amount' => $paid,
        ])]);

        return $this->postJson('/webhooks/pembayaran', ['id' => $order->gateway_ref, 'external_id' => $order->public_id, 'status' => $status], ['x-callback-token' => 'cb-token']);
    }
}
