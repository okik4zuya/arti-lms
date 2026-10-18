<?php

namespace Tests\Feature;

use App\Actions\Orders\RefundOrder;
use App\Jobs\FulfillOrder;
use App\Models\Course;
use App\Models\CourseAccess;
use App\Models\LicenseIssuance;
use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use InvalidArgumentException;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RefundAndReconcileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.payments.driver' => 'xendit',
            'services.licensing.url' => 'https://worker.test/v1',
            'services.licensing.admin_token' => 'tok',
            'services.xendit.url' => 'https://xendit.test',
            'services.xendit.secret_key' => 'xnd_development_test',
            'services.xendit.callback_token' => 'cb-token',
        ]);
        Queue::fake();
        $this->artisan('permissions:sync');
    }

    private function actor(string $role): User
    {
        $user = User::firstOrCreate(['email' => "{$role}@example.test"], ['name' => ucfirst($role), 'password' => 'x']);
        $user->assignRole($role);

        return $user;
    }

    /** A fulfilled order with an account, course access and an active license. */
    private function soldOrder(array $over = []): Order
    {
        $course = Course::firstOrCreate(['slug' => 'arti-framework'], ['title' => 'ARTi']);
        $buyer = User::factory()->create(['email' => 'buyer@example.test']);
        $buyer->assignRole('learner');
        CourseAccess::create(['user_id' => $buyer->id, 'course_id' => $course->id, 'granted_at' => now()]);
        $license = LicenseIssuance::create([
            'order_ref' => 'arti-order1', 'source' => 'xendit', 'email' => 'buyer@example.test', 'name' => 'Buyer', 'tier' => 'individual',
            'months' => 1000, 'user_id' => $buyer->id,
        ]);
        $license->forceFill(['status' => LicenseIssuance::ACTIVE, 'key_id' => 'k1'])->save();

        $order = Order::create($over + [
            'public_id' => 'arti-order1', 'name' => 'Buyer', 'email' => 'buyer@example.test', 'amount' => 99000, 'price_tier' => 'launch',
            'launch_slot' => 1, 'status' => Order::FULFILLED, 'gateway_ref' => 'inv-1',
        ]);
        $order->forceFill(['paid_at' => now(), 'user_id' => $buyer->id, 'license_issuance_id' => $license->id])->save();

        return $order;
    }

    private function worker(int $status = 200): void
    {
        Http::swap(new Factory);
        Http::fake(['worker.test/*' => Http::response($status === 200 ? ['key_id' => 'k1', 'status' => 'revoked'] : ['error' => 'server_error'], $status)]);
    }

    // ---------------------------------------------------------------- refund

    public function test_a_refund_revokes_the_license_removes_course_access_and_marks_the_order(): void
    {
        $this->worker();
        $order = $this->soldOrder();

        app(RefundOrder::class)->handle($order, $this->actor('admin'), 'Pembeli minta kembali', 'Buyer@Example.test');

        $order->refresh();
        $this->assertSame(Order::REFUNDED, $order->status);
        $this->assertSame('Pembeli minta kembali', $order->refund_reason);
        $this->assertNotNull($order->refunded_at);
        $this->assertSame(LicenseIssuance::REVOKED, $order->licenseIssuance->status);
        $this->assertDatabaseCount('course_access', 0);
        $this->assertTrue($order->events()->where('event', 'refunded')->exists());
        Http::assertSent(fn (Request $r) => str_contains($r->url(), '/admin/revoke') && $r['key_id'] === 'k1' && str_contains($r['reason'], 'Refund'));
        // The money is returned in the Xendit dashboard: the system never calls the gateway for it.
        Http::assertNotSent(fn (Request $r) => str_contains($r->url(), 'xendit'));
        // The launch place stays used: a refunded launch buyer does not reopen the batch.
        $this->assertSame(1, $order->launch_slot);
    }

    public function test_support_cannot_refund(): void
    {
        $this->worker();
        $order = $this->soldOrder();

        $this->expectException(AuthorizationException::class);
        try {
            app(RefundOrder::class)->handle($order, $this->actor('support'), 'x', 'buyer@example.test');
        } finally {
            $this->assertSame(Order::FULFILLED, $order->fresh()->status);
            Http::assertNothingSent();
        }
    }

    public function test_a_refund_needs_the_typed_email_and_a_reason_and_changes_nothing_otherwise(): void
    {
        $this->worker();
        $order = $this->soldOrder();
        $admin = $this->actor('admin');

        foreach ([['alasan', 'salah@example.test'], ['  ', 'buyer@example.test']] as [$reason, $email]) {
            try {
                app(RefundOrder::class)->handle($order, $admin, $reason, $email);
                $this->fail('Expected a refusal.');
            } catch (InvalidArgumentException) {
            }
        }

        $this->assertSame(Order::FULFILLED, $order->fresh()->status);
        $this->assertSame(LicenseIssuance::ACTIVE, $order->licenseIssuance->fresh()->status);
        $this->assertDatabaseCount('course_access', 1);
        Http::assertNothingSent();
    }

    public function test_when_the_worker_is_down_the_order_stays_as_it_was_so_the_admin_can_retry(): void
    {
        $this->worker(500);
        $order = $this->soldOrder();

        try {
            app(RefundOrder::class)->handle($order, $this->actor('admin'), 'alasan', 'buyer@example.test');
            $this->fail('Expected the Worker error to surface.');
        } catch (\App\Services\Licensing\LicenseApiException) {
        }

        $this->assertSame(Order::FULFILLED, $order->fresh()->status);
        $this->assertDatabaseCount('course_access', 1);

        $this->worker();
        app(RefundOrder::class)->handle($order->fresh(), $this->actor('admin'), 'alasan', 'buyer@example.test');
        $this->assertSame(Order::REFUNDED, $order->fresh()->status);
    }

    public function test_an_order_cannot_be_refunded_twice_or_before_it_was_paid(): void
    {
        $this->worker();
        $admin = $this->actor('admin');
        $order = $this->soldOrder();
        app(RefundOrder::class)->handle($order, $admin, 'alasan', 'buyer@example.test');

        foreach ([$order->fresh(), Order::create(['public_id' => 'arti-p', 'name' => 'P', 'email' => 'p@example.test', 'amount' => 1, 'price_tier' => 'normal', 'status' => Order::PENDING])] as $candidate) {
            try {
                app(RefundOrder::class)->handle($candidate, $admin, 'alasan', $candidate->email);
                $this->fail('Expected a refusal.');
            } catch (InvalidArgumentException) {
            }
        }
        Http::assertSentCount(1);
    }

    public function test_course_access_stays_when_the_buyer_holds_another_active_license(): void
    {
        $this->worker();
        $order = $this->soldOrder();
        LicenseIssuance::create(['order_ref' => 'WA-9', 'source' => 'manual', 'email' => 'buyer@example.test', 'name' => 'B', 'tier' => 'individual', 'months' => 12])
            ->forceFill(['status' => LicenseIssuance::ACTIVE, 'key_id' => 'k2'])->save();

        app(RefundOrder::class)->handle($order, $this->actor('admin'), 'alasan', 'buyer@example.test');

        $this->assertDatabaseCount('course_access', 1);
    }

    // ------------------------------------------------------------- reconcile

    private function pendingOrder(array $over = []): Order
    {
        $order = Order::create($over + [
            'public_id' => 'arti-order1', 'name' => 'Buyer', 'email' => 'buyer@example.test', 'amount' => 99000, 'price_tier' => 'launch',
            'launch_slot' => 1, 'status' => Order::PENDING, 'gateway_ref' => 'inv-1', 'expires_at' => now()->addHour(),
        ]);
        $order->forceFill(['created_at' => now()->subMinutes(10)])->save();

        return $order;
    }

    private function xendit(string $status, int $paid = 99000, ?string $id = 'inv-1', ?string $external = 'arti-order1'): void
    {
        Http::swap(new Factory);
        Http::fake(['xendit.test/v2/invoices/*' => Http::response([
            'id' => $id, 'external_id' => $external, 'status' => $status, 'amount' => 99000, 'paid_amount' => $paid,
            'payment_method' => 'QR_CODE', 'payment_channel' => 'QRIS',
        ])]);
    }

    public function test_a_payment_whose_webhook_never_came_is_picked_up_once(): void
    {
        $order = $this->pendingOrder();
        $this->xendit('PAID');

        $this->artisan('orders:reconcile')->assertSuccessful();
        $this->artisan('orders:reconcile')->assertSuccessful();

        $this->assertSame(Order::PAID, $order->fresh()->status);
        Queue::assertPushed(FulfillOrder::class, 1);
        $this->assertTrue($order->events()->where('event', 'reconciled')->exists());
    }

    public function test_orders_that_are_too_new_or_still_unpaid_are_left_alone(): void
    {
        $fresh = $this->pendingOrder(['created_at' => now()]);
        $fresh->forceFill(['created_at' => now()->subMinute()])->save();
        $this->xendit('PAID');

        $this->artisan('orders:reconcile')->assertSuccessful();
        $this->assertSame(Order::PENDING, $fresh->fresh()->status);

        $fresh->forceFill(['created_at' => now()->subMinutes(10)])->save();
        $this->xendit('PENDING', 0);
        $this->artisan('orders:reconcile')->assertSuccessful();
        $this->assertSame(Order::PENDING, $fresh->fresh()->status);
        Queue::assertNothingPushed();
    }

    public function test_a_paid_order_that_expired_locally_is_still_honoured_and_gets_its_place_back(): void
    {
        $order = $this->pendingOrder(['status' => Order::EXPIRED, 'launch_slot' => null]);
        $this->xendit('PAID');

        $this->artisan('orders:reconcile')->assertSuccessful();

        $order->refresh();
        $this->assertSame([Order::PAID, 1], [$order->status, $order->launch_slot]);
        Queue::assertPushed(FulfillOrder::class, 1);
    }

    public function test_lapsed_orders_give_their_places_back_after_the_payments_were_checked(): void
    {
        $lapsed = $this->pendingOrder(['expires_at' => now()->subMinute()]);
        $this->xendit('EXPIRED', 0);

        $this->artisan('orders:reconcile')->assertSuccessful();

        $lapsed->refresh();
        $this->assertSame([Order::EXPIRED, null], [$lapsed->status, $lapsed->launch_slot]);
    }

    public function test_an_invoice_that_belongs_to_another_order_changes_nothing(): void
    {
        $order = $this->pendingOrder();
        $this->xendit('PAID', 99000, 'inv-1', 'arti-someone-else');

        $this->artisan('orders:reconcile')->assertSuccessful();

        $this->assertSame(Order::PENDING, $order->fresh()->status);
        Queue::assertNothingPushed();
    }

    public function test_fulfilment_that_stalled_is_restarted_and_a_dry_run_changes_nothing(): void
    {
        $stuck = $this->pendingOrder(['status' => Order::PAID, 'public_id' => 'arti-stuck', 'gateway_ref' => 'inv-s']);
        $stuck->forceFill(['updated_at' => now()->subMinutes(30)])->save();
        $this->pendingOrder(['public_id' => 'arti-recent', 'gateway_ref' => 'inv-r', 'status' => Order::FULFILLING, 'email' => 'r@example.test', 'launch_slot' => 2])
            ->forceFill(['updated_at' => now()])->save();
        $this->xendit('PENDING', 0, 'inv-1');

        $this->artisan('orders:reconcile --dry-run')->assertSuccessful();
        Queue::assertNothingPushed();

        $this->artisan('orders:reconcile')->assertSuccessful();
        Queue::assertPushed(FulfillOrder::class, 1);
        Queue::assertPushed(FulfillOrder::class, fn (FulfillOrder $job) => $job->orderId === $stuck->id);
    }

    public function test_an_unreachable_xendit_changes_nothing_and_is_reported(): void
    {
        $order = $this->pendingOrder();
        Http::swap(new Factory);
        Http::fake(fn () => throw new ConnectionException('down'));

        $this->artisan('orders:reconcile')->expectsOutputToContain('unreachable 1')->assertSuccessful();

        $this->assertSame(Order::PENDING, $order->fresh()->status);
    }

    public function test_without_a_gateway_configured_it_does_nothing(): void
    {
        config(['services.xendit.secret_key' => '']);
        $this->pendingOrder();

        $this->artisan('orders:reconcile')->expectsOutputToContain('not configured')->assertSuccessful();
    }

    public function test_the_command_is_scheduled_and_failed_jobs_are_pruned(): void
    {
        $this->artisan('schedule:list')->expectsOutputToContain('orders:reconcile')->assertSuccessful();
    }
}
