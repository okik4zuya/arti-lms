<?php

namespace Tests\Feature;

use App\Actions\Orders\FulfillPaidOrder;
use App\Jobs\FulfillOrder;
use App\Mail\OrderAttentionMail;
use App\Mail\PurchaseMail;
use App\Models\Course;
use App\Models\LicenseIssuance;
use App\Models\Order;
use App\Models\User;
use App\Services\Licensing\LicenseApiException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FulfillOrderTest extends TestCase
{
    use RefreshDatabase;

    private const KEY = 'ARTI-ABCD-EFGH-JKMN-PQRS';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.payments.driver' => 'xendit',
            'services.licensing.url' => 'https://worker.test/v1',
            'services.licensing.admin_token' => 'tok',
            'services.licensing.installer_url' => 'https://cdn.example.test/installers/ArtiFrameworkSetup-0.1.9.exe',
            'services.xendit.url' => 'https://xendit.test',
            'services.xendit.secret_key' => 'xnd_development_test',
            'services.xendit.callback_token' => 'cb-token',
        ]);
        Role::findOrCreate('learner');
        Role::findOrCreate('admin');
        Mail::fake();
    }

    private function course(): Course
    {
        return Course::create(['slug' => 'arti-framework', 'title' => 'ARTi Framework']);
    }

    private function paidOrder(array $over = []): Order
    {
        return Order::create($over + [
            'public_id' => 'arti-order1', 'name' => 'Budi Pembeli', 'email' => 'buyer@example.test', 'amount' => 99000,
            'price_tier' => 'launch', 'status' => Order::PAID, 'gateway' => 'xendit', 'gateway_ref' => 'inv-1', 'whatsapp' => '6281234567890',
        ]);
    }

    private function issued(array $over = []): array
    {
        return $over + [
            'key' => self::KEY, 'key_id' => 'k1', 'tier' => 'individual', 'seats' => 1,
            'expires' => '2109-10-15T10:00:00.000Z', 'updates_until' => '2109-10-15T10:00:00.000Z', 'email' => 'buyer@example.test',
        ];
    }

    private function worker(?array $body = null, int $status = 200): void
    {
        Http::swap(new Factory);
        Http::fake(['worker.test/*' => Http::response($body ?? $this->issued(), $status)]);
    }

    private function fulfil(Order $order): void
    {
        app(FulfillPaidOrder::class)->handle($order->fresh());
    }

    private function sentToWorker(): int
    {
        return Http::recorded(fn (Request $r) => str_contains($r->url(), '/admin/issue'))->count();
    }

    public function test_a_paid_order_becomes_an_account_a_license_and_one_mail(): void
    {
        $this->course();
        $this->worker();
        $order = $this->paidOrder();

        $this->fulfil($order);

        $order->refresh();
        $this->assertSame(Order::FULFILLED, $order->status);
        $this->assertNotNull($order->fulfilled_at);

        $user = User::where('email', 'buyer@example.test')->firstOrFail();
        $this->assertTrue($user->hasRole('learner'));
        $this->assertSame($user->id, $order->user_id);
        $this->assertDatabaseHas('course_access', ['user_id' => $user->id, 'course_id' => Course::first()->id]);

        $license = LicenseIssuance::firstOrFail();
        $this->assertSame($license->id, $order->license_issuance_id);
        $this->assertSame('arti-order1', $license->order_ref);
        $this->assertSame('xendit', $license->source);
        $this->assertNotNull($license->payment_verified_at);
        $this->assertSame(self::KEY, $license->key);
        $this->assertSame($user->id, $license->user_id);

        Http::assertSent(fn (Request $r) => str_contains($r->url(), '/admin/issue')
            && $r['months'] === 1000 && $r['order'] === 'arti-order1' && $r['amount_idr'] === 99000 && $r['email'] === 'buyer@example.test');

        Mail::assertQueued(PurchaseMail::class, 1);
        Mail::assertQueued(PurchaseMail::class, fn (PurchaseMail $m) => $m->hasTo('buyer@example.test') && str_contains((string) $m->setPasswordUrl, 'b=welcome'));
    }

    public function test_the_purchase_mail_has_account_link_key_and_download_but_no_password(): void
    {
        $this->course();
        $this->worker();
        $order = $this->paidOrder();
        $this->fulfil($order);

        $url = null;
        Mail::assertQueued(PurchaseMail::class, function (PurchaseMail $m) use (&$url) {
            $url = $m->setPasswordUrl;

            return true;
        });
        $html = (new PurchaseMail($order->id, $url))->render();

        $this->assertStringContainsString('buyer@example.test', $html);
        $this->assertStringContainsString(self::KEY, $html);
        // The set-password link is also written out, for a mail client where the button does not work.
        $this->assertStringContainsString('Kalau tombol tidak berfungsi', $html);
        $this->assertStringContainsString('>'.htmlspecialchars((string) $url).'</a>', $html);
        $this->assertStringContainsString('selamanya', $html);
        $this->assertStringContainsString(htmlspecialchars($url), $html);
        // No direct installer link and no terminal step: the buyer is sent to the Download page of the course.
        $this->assertStringContainsString('/download-installer', $html);
        $this->assertStringNotContainsString('ArtiFrameworkSetup', $html);
        $this->assertStringNotContainsString('cdn.example.test', $html);
        $this->assertStringNotContainsString('arti-license', $html);
        $this->assertStringNotContainsString('terminal', $html);
        $this->assertStringContainsString('Password tidak pernah dikirim', $html);
        $this->assertStringNotContainsString('sementara', $html);
        $this->assertStringContainsString('tidak ada batas waktunya', $html);
        $this->assertStringNotContainsString('jam dan hanya', $html);

        // The queue payload carries the order id and the link, never the key.
        $this->assertStringNotContainsString(self::KEY, serialize(new PurchaseMail($order->id, $url)));
    }

    public function test_the_link_in_the_mail_sets_the_password_once(): void
    {
        $this->course();
        $this->worker();
        $this->fulfil($this->paidOrder());
        $url = null;
        Mail::assertQueued(PurchaseMail::class, function (PurchaseMail $m) use (&$url) {
            $url = $m->setPasswordUrl;

            return true;
        });

        $this->get($url)->assertOk()->assertSee('Atur password akun belajar');
        $this->post($url, ['password' => 'sandi-pembeli-77x', 'password_confirmation' => 'sandi-pembeli-77x'])->assertRedirect(route('login'));
        $this->post('/login', ['email' => 'buyer@example.test', 'password' => 'sandi-pembeli-77x'])->assertRedirect(route('dashboard'));
        $this->post('/logout');
        $this->get($url)->assertSee('Tautan tidak berlaku');
    }

    public function test_running_fulfilment_again_issues_one_key_and_sends_one_mail(): void
    {
        $this->course();
        $this->worker();
        $order = $this->paidOrder();

        $this->fulfil($order);
        $this->fulfil($order);
        (new FulfillOrder($order->id))->handle(app(FulfillPaidOrder::class));

        $this->assertSame(1, $this->sentToWorker());
        $this->assertSame(1, LicenseIssuance::count());
        $this->assertSame(1, User::count());
        Mail::assertQueued(PurchaseMail::class, 1);
        $this->assertSame(1, $order->events()->where('event', 'fulfilled')->count());
    }

    public function test_a_buyer_who_already_has_an_account_keeps_the_password_and_gets_no_link(): void
    {
        $this->course();
        $this->worker();
        $user = User::factory()->create(['email' => 'buyer@example.test', 'password' => 'sandi-lama-123']);
        $user->assignRole('learner');
        $order = $this->paidOrder(['email' => 'Buyer@Example.test']);

        $this->fulfil($order);

        $this->assertTrue(Hash::check('sandi-lama-123', $user->fresh()->password));
        $this->assertSame(1, User::count());
        $this->assertDatabaseHas('course_access', ['user_id' => $user->id]);
        Mail::assertQueued(PurchaseMail::class, fn (PurchaseMail $m) => $m->setPasswordUrl === null);
        $this->assertSame(Order::FULFILLED, $order->fresh()->status);
    }

    public function test_a_staff_email_is_parked_for_an_admin_and_nothing_is_issued(): void
    {
        $this->worker();
        $admin = User::factory()->create(['email' => 'boss@example.test']);
        $admin->assignRole('admin');
        $order = $this->paidOrder(['email' => 'boss@example.test']);

        $this->fulfil($order);

        $order->refresh();
        $this->assertSame(Order::NEEDS_ATTENTION, $order->status);
        $this->assertSame('staff_email', $order->attention_note);
        $this->assertSame(0, LicenseIssuance::count());
        Mail::assertNotQueued(PurchaseMail::class);
        Mail::assertQueued(OrderAttentionMail::class, fn (OrderAttentionMail $m) => $m->hasTo('boss@example.test') && $m->note === 'staff_email');
    }

    public function test_a_second_purchase_for_an_active_license_is_parked_and_the_admin_is_told(): void
    {
        $this->course();
        $this->worker();
        $admin = User::factory()->create(['email' => 'boss@example.test']);
        $admin->assignRole('admin');
        LicenseIssuance::create([
            'order_ref' => 'WA-1', 'source' => 'manual', 'email' => 'buyer@example.test', 'name' => 'Buyer', 'tier' => 'individual',
            'months' => 12,
        ])->forceFill(['status' => LicenseIssuance::ACTIVE])->save();
        $order = $this->paidOrder();

        $this->fulfil($order);

        $order->refresh();
        $this->assertSame(Order::NEEDS_ATTENTION, $order->status);
        $this->assertSame('duplicate_purchase', $order->attention_note);
        $this->assertSame(0, $this->sentToWorker());
        Mail::assertNotQueued(PurchaseMail::class);
        Mail::assertQueued(OrderAttentionMail::class, fn (OrderAttentionMail $m) => $m->hasTo('boss@example.test') && $m->note === 'duplicate_purchase');
    }

    public function test_an_unreachable_worker_keeps_the_order_in_progress_and_the_retry_finishes_it(): void
    {
        $this->course();
        $order = $this->paidOrder();

        $this->worker(['error' => 'server_error', 'message' => 'x'], 500);
        try {
            $this->fulfil($order);
            $this->fail('The unreachable Worker should stop the job so it is retried.');
        } catch (LicenseApiException) {
        }

        $order->refresh();
        $this->assertSame(Order::FULFILLING, $order->status);
        $this->assertNull($order->license_issuance_id);
        $this->assertNull($order->email_sent_at);
        Mail::assertNothingQueued();

        $this->worker();
        $this->fulfil($order);

        $this->assertSame(Order::FULFILLED, $order->fresh()->status);
        $this->assertSame(1, LicenseIssuance::count());
        Mail::assertQueued(PurchaseMail::class, 1);
    }

    public function test_when_the_retries_run_out_the_order_is_parked_with_a_note_that_holds_no_secret(): void
    {
        $this->course();
        $this->worker();
        $order = $this->paidOrder();
        $admin = User::factory()->create(['email' => 'boss@example.test']);
        $admin->assignRole('admin');
        $this->fulfil($order->refresh()->forceFill(['status' => Order::FULFILLING]));

        $order = $this->paidOrder(['public_id' => 'arti-order2', 'gateway_ref' => 'inv-2', 'email' => 'second@example.test', 'status' => Order::FULFILLING]);
        (new FulfillOrder($order->id))->failed(new RuntimeException('boom '.self::KEY));

        $order->refresh();
        $this->assertSame(Order::NEEDS_ATTENTION, $order->status);
        $this->assertSame('fulfil_failed', $order->attention_note);
        $this->assertSame('RuntimeException', $order->events()->first()->detail);
        Mail::assertQueued(OrderAttentionMail::class, fn (OrderAttentionMail $m) => $m->orderId === $order->id);
    }

    public function test_a_missing_course_does_not_stop_the_sale(): void
    {
        $this->worker();
        $order = $this->paidOrder();

        $this->fulfil($order);

        $order->refresh();
        $this->assertSame(Order::FULFILLED, $order->status);
        $this->assertSame('course_missing', $order->attention_note);
        $this->assertDatabaseCount('course_access', 0);
    }

    public function test_an_order_that_is_not_paid_is_left_alone(): void
    {
        $this->worker();
        $pending = $this->paidOrder(['status' => Order::PENDING]);
        $expired = $this->paidOrder(['public_id' => 'arti-order2', 'gateway_ref' => 'inv-2', 'status' => Order::EXPIRED]);
        $refunded = $this->paidOrder(['public_id' => 'arti-order3', 'gateway_ref' => 'inv-3', 'status' => Order::REFUNDED]);

        foreach ([$pending, $expired, $refunded] as $order) {
            $this->fulfil($order);
        }

        $this->assertSame(0, $this->sentToWorker());
        $this->assertSame(0, User::count());
        Mail::assertNothingQueued();
    }

    public function test_no_key_or_token_is_written_to_the_order_events_or_the_license_events(): void
    {
        $this->course();
        $this->worker();
        $order = $this->paidOrder();
        $this->fulfil($order);

        $log = json_encode(DB::table('order_events')->get()).json_encode(DB::table('license_issuance_events')->get());

        $this->assertStringNotContainsString(self::KEY, $log);
        $this->assertStringNotContainsString('token', strtolower($log));
        $this->assertStringNotContainsString('signature', strtolower($log));
    }

    public function test_the_webhook_runs_the_whole_chain(): void
    {
        $this->course();
        $order = $this->paidOrder(['status' => Order::PENDING]);
        Http::swap(new Factory);
        Http::fake([
            'xendit.test/v2/invoices/*' => Http::response([
                'id' => 'inv-1', 'external_id' => 'arti-order1', 'status' => 'PAID', 'amount' => 99000, 'paid_amount' => 99000,
                'payment_method' => 'QR_CODE', 'payment_channel' => 'QRIS',
            ]),
            'worker.test/*' => Http::response($this->issued()),
        ]);

        $this->postJson('/webhooks/pembayaran', ['id' => 'inv-1', 'external_id' => 'arti-order1', 'status' => 'PAID'], ['x-callback-token' => 'cb-token'])->assertOk();
        $this->postJson('/webhooks/pembayaran', ['id' => 'inv-1', 'external_id' => 'arti-order1', 'status' => 'PAID'], ['x-callback-token' => 'cb-token'])->assertOk();

        $this->assertSame(Order::FULFILLED, $order->fresh()->status);
        $this->assertSame(1, $this->sentToWorker());
        Mail::assertQueued(PurchaseMail::class, 1);
    }
}
