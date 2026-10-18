<?php

namespace Tests\Feature;

use App\Filament\Resources\OrderResource;
use App\Filament\Resources\OrderResource\Pages\ListOrders;
use App\Jobs\FulfillOrder;
use App\Mail\PurchaseMail;
use App\Models\LicenseIssuance;
use App\Models\Order;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use Tests\TestCase;

class FilamentOrderTest extends TestCase
{
    use RefreshDatabase;

    private const KEY = 'ARTI-ABCD-EFGH-JKMN-PQRS';

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.licensing.url' => 'https://worker.test/v1', 'services.licensing.admin_token' => 'tok']);
        $this->artisan('permissions:sync')->assertSuccessful();
        Filament::setCurrentPanel('jamrud');
        Mail::fake();
        Queue::fake();
    }

    private function as(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);
        $this->actingAs($user);

        return $user;
    }

    private function order(array $over = [], array $force = []): Order
    {
        $order = Order::create($over + [
            'public_id' => 'arti-order1', 'name' => 'Budi', 'email' => 'buyer@example.test', 'amount' => 99000, 'price_tier' => 'launch',
            'launch_slot' => 1, 'status' => Order::FULFILLED, 'gateway_ref' => 'inv-1',
        ]);
        $order->forceFill($force + ['paid_at' => now()])->save();

        return $order;
    }

    private function fulfilledWithAccount(): Order
    {
        $buyer = User::factory()->create(['email' => 'buyer@example.test']);
        $buyer->assignRole('learner');
        $license = LicenseIssuance::create(['order_ref' => 'arti-order1', 'source' => 'xendit', 'email' => 'buyer@example.test', 'name' => 'Budi', 'tier' => 'individual', 'months' => 1000, 'user_id' => $buyer->id]);
        $license->forceFill(['status' => LicenseIssuance::ACTIVE, 'key_id' => 'k1', 'key' => self::KEY])->save();

        return $this->order([], ['user_id' => $buyer->id, 'license_issuance_id' => $license->id]);
    }

    public function test_admin_and_support_may_open_the_list_learners_may_not(): void
    {
        $this->as('support');
        $this->assertTrue(OrderResource::canViewAny());
        $this->get('/jamrud/orders')->assertOk();

        $this->as('learner');
        $this->assertFalse(OrderResource::canViewAny());
        $this->get('/jamrud/orders')->assertForbidden();
    }

    public function test_the_list_shows_orders_but_never_a_key_or_a_link(): void
    {
        $this->as('admin');
        $order = $this->fulfilledWithAccount();

        Livewire::test(ListOrders::class)
            ->assertCanSeeTableRecords([$order])
            ->assertSee('Rp99.000')
            ->assertSee('Perintis')
            ->assertSee('Selesai')
            ->assertDontSee(self::KEY)
            ->assertDontSee('atur-sandi');
    }

    public function test_filters_find_orders_that_need_a_decision(): void
    {
        $this->as('admin');
        $ok = $this->order();
        $parked = $this->order(['public_id' => 'arti-p', 'email' => 'p@example.test', 'gateway_ref' => 'inv-p', 'launch_slot' => 2, 'status' => Order::NEEDS_ATTENTION, 'price_tier' => 'promo']);
        $parked->forceFill(['attention_note' => 'duplicate_purchase'])->save();

        Livewire::test(ListOrders::class)
            ->filterTable('status', [Order::NEEDS_ATTENTION])
            ->assertCanSeeTableRecords([$parked])
            ->assertCanNotSeeTableRecords([$ok])
            ->assertSee('Sudah punya lisensi aktif');
    }

    public function test_support_can_resend_the_purchase_mail_but_not_refund_or_resolve(): void
    {
        $this->as('support');
        $order = $this->fulfilledWithAccount();

        Livewire::test(ListOrders::class)
            ->assertActionVisible(TestAction::make('resend')->table($order))
            ->assertActionHidden(TestAction::make('refund')->table($order))
            ->assertActionHidden(TestAction::make('resolve')->table($order))
            ->callAction(TestAction::make('resend')->table($order));

        Mail::assertQueued(PurchaseMail::class, fn (PurchaseMail $m) => $m->hasTo('buyer@example.test') && str_contains((string) $m->setPasswordUrl, 'b=welcome'));
        $this->assertTrue($order->events()->where('event', 'email_resent')->exists());
    }

    public function test_the_resent_link_replaces_the_old_one_and_works_once(): void
    {
        $this->as('admin');
        $order = $this->fulfilledWithAccount();
        $first = app(\App\Actions\Account\IssueSetPasswordLink::class)->handle($order->user, 'welcome');

        Livewire::test(ListOrders::class)->callAction(TestAction::make('resend')->table($order));

        $url = null;
        Mail::assertQueued(PurchaseMail::class, function (PurchaseMail $m) use (&$url) {
            $url = $m->setPasswordUrl;

            return true;
        });
        auth()->logout();
        $this->get($first)->assertSee('Tautan tidak berlaku');
        $this->get($url)->assertSee('Atur password akun belajar');
    }

    public function test_an_admin_can_refund_with_the_typed_email_and_the_license_is_revoked(): void
    {
        $this->as('admin');
        $order = $this->fulfilledWithAccount();
        Http::swap(new Factory);
        Http::fake(['worker.test/*' => Http::response(['key_id' => 'k1', 'status' => 'revoked'])]);

        Livewire::test(ListOrders::class)
            ->callAction(TestAction::make('refund')->table($order), ['reason' => 'Pembeli minta kembali', 'confirm_email' => 'wrong@example.test'])
            ->assertNotified();
        $this->assertSame(Order::FULFILLED, $order->fresh()->status);

        Livewire::test(ListOrders::class)
            ->callAction(TestAction::make('refund')->table($order), ['reason' => 'Pembeli minta kembali', 'confirm_email' => 'buyer@example.test']);

        $order->refresh();
        $this->assertSame(Order::REFUNDED, $order->status);
        $this->assertSame(LicenseIssuance::REVOKED, $order->licenseIssuance->status);
    }

    public function test_a_parked_order_can_be_continued_but_an_amount_mismatch_cannot(): void
    {
        $this->as('admin');
        $duplicate = $this->order(['status' => Order::NEEDS_ATTENTION], []);
        $duplicate->forceFill(['attention_note' => 'duplicate_purchase'])->save();
        $mismatch = $this->order(['public_id' => 'arti-m', 'email' => 'm@example.test', 'gateway_ref' => 'inv-m', 'launch_slot' => 2, 'status' => Order::NEEDS_ATTENTION]);
        $mismatch->forceFill(['attention_note' => 'amount_mismatch'])->save();

        Livewire::test(ListOrders::class)
            ->assertActionHidden(TestAction::make('resolve')->table($mismatch))
            ->callAction(TestAction::make('resolve')->table($duplicate));

        $duplicate->refresh();
        $this->assertSame([Order::PAID, 'approved'], [$duplicate->status, $duplicate->attention_note]);
        Queue::assertPushed(FulfillOrder::class, 1);
    }

    public function test_the_history_shows_the_events_of_an_order(): void
    {
        $this->as('support');
        $order = $this->order();
        $order->logEvent('paid', 'QR_CODE QRIS');
        $order->logEvent('license_issued');

        // Same approach as the license history: the modal body is a plain view, so render it.
        $html = view('filament.order-history', ['order' => $order, 'events' => $order->events()->get()])->render();

        $this->assertStringContainsString('QR_CODE QRIS', $html);
        $this->assertStringContainsString('license_issued', $html);
        $this->assertStringContainsString('buyer@example.test', $html);
        $this->assertStringNotContainsString(self::KEY, $html);

        Livewire::test(ListOrders::class)->mountAction(TestAction::make('history')->table($order))->assertHasNoErrors();
    }

    public function test_the_menu_badge_counts_parked_orders_for_admins_only(): void
    {
        $parked = $this->order(['status' => Order::NEEDS_ATTENTION]);
        $this->order(['public_id' => 'arti-x', 'email' => 'x@example.test', 'gateway_ref' => 'inv-x', 'launch_slot' => 2]);

        $this->as('admin');
        $this->assertSame('1', OrderResource::getNavigationBadge());

        $this->as('support');
        $this->assertNull(OrderResource::getNavigationBadge());
        $this->assertNotNull($parked);
    }
}
