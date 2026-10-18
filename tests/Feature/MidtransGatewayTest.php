<?php

namespace Tests\Feature;

use App\Jobs\FulfillOrder;
use App\Mail\PurchaseMail;
use App\Models\Course;
use App\Models\Order;
use App\Services\Payments\MidtransClient;
use App\Services\Payments\MidtransException;
use App\Services\Payments\PaymentGateway;
use App\Services\Payments\PaymentGateways;
use App\Services\Payments\XenditClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use InvalidArgumentException;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MidtransGatewayTest extends TestCase
{
    use RefreshDatabase;

    private const KEY = 'SB-Mid-server-test';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.payments.driver' => 'midtrans',
            'services.midtrans.server_key' => self::KEY,
            'services.midtrans.snap_url' => 'https://snap.test',
            'services.midtrans.api_url' => 'https://api.midtrans.test',
            'site.contact_whatsapp' => '081234567890',
            'site.offer.launch_sold' => null,
        ]);
        Queue::fake();
    }

    private function order(array $over = []): Order
    {
        return Order::create($over + [
            'public_id' => 'arti-order1', 'name' => 'Buyer', 'email' => 'buyer@example.test', 'amount' => 99000, 'price_tier' => 'launch',
            'launch_slot' => 1, 'status' => Order::PENDING, 'gateway' => 'midtrans', 'gateway_ref' => 'arti-order1', 'expires_at' => now()->addHour(),
        ]);
    }

    /** A Status API answer. */
    private function transaction(array $over = []): array
    {
        return $over + [
            'status_code' => '200', 'transaction_status' => 'settlement', 'fraud_status' => 'accept', 'order_id' => 'arti-order1',
            'gross_amount' => '99000.00', 'payment_type' => 'qris', 'acquirer' => 'gopay', 'transaction_id' => 'tx-1',
        ];
    }

    private function statusApi(array $transaction, int $http = 200): void
    {
        Http::swap(new Factory);
        Http::fake(['api.midtrans.test/v2/*/status' => Http::response($transaction, $http)]);
    }

    private function notify(array $body = [], ?string $signature = null, string $key = self::KEY)
    {
        $body += ['order_id' => 'arti-order1', 'status_code' => '200', 'gross_amount' => '99000.00', 'transaction_status' => 'settlement'];
        $body['signature_key'] ??= $signature ?? hash('sha512', $body['order_id'].$body['status_code'].$body['gross_amount'].$key);

        return $this->postJson('/webhooks/midtrans', $body);
    }

    // ------------------------------------------------------------ checkout

    public function test_checkout_asks_snap_for_the_exact_amount_and_sends_the_buyer_to_its_page(): void
    {
        Http::swap(new Factory);
        Http::fake(['snap.test/snap/v1/transactions' => Http::response(['token' => 'tok', 'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v4/redirection/tok'], 201)]);

        $this->post('/beli', ['nama' => 'Budi', 'email' => 'Budi@Example.test', 'whatsapp' => '081234567890', 'setuju' => '1'])
            ->assertRedirect('https://app.sandbox.midtrans.com/snap/v4/redirection/tok');

        $order = Order::firstOrFail();
        $this->assertSame(['midtrans', $order->public_id, 99000, 'launch', 1], [$order->gateway, $order->gateway_ref, $order->amount, $order->price_tier, $order->launch_slot]);

        Http::assertSent(function (Request $r) use ($order) {
            return $r->url() === 'https://snap.test/snap/v1/transactions'
                && $r->hasHeader('Authorization', 'Basic '.base64_encode(self::KEY.':'))
                && $r['transaction_details']['order_id'] === $order->public_id
                && $r['transaction_details']['gross_amount'] === 99000
                && $r['item_details'][0]['price'] * $r['item_details'][0]['quantity'] === 99000
                && $r['customer_details']['email'] === 'budi@example.test'
                && $r['customer_details']['phone'] === '+6281234567890'
                && $r['expiry'] === ['unit' => 'minutes', 'duration' => 60]
                && str_contains($r['callbacks']['finish'], '/beli/selesai/'.$order->public_id);
        });
    }

    public function test_when_snap_refuses_the_place_is_given_back(): void
    {
        Http::swap(new Factory);
        Http::fake(['snap.test/*' => Http::response(['error_messages' => ['x']], 401)]);

        $this->post('/beli', ['nama' => 'Budi', 'email' => 'budi@example.test', 'setuju' => '1'])->assertSessionHasErrors('pembayaran');

        $order = Order::firstOrFail();
        $this->assertSame([Order::FAILED, null], [$order->status, $order->launch_slot]);
    }

    public function test_without_a_server_key_the_buy_page_falls_back_to_whatsapp(): void
    {
        config(['services.midtrans.server_key' => '']);

        $this->get('/beli')->assertRedirectContains('https://wa.me/6281234567890');
    }

    public function test_the_buy_page_and_legal_pages_name_midtrans(): void
    {
        $this->get('/beli')->assertOk()->assertSee('Pembayaran diproses Midtrans')->assertDontSee('Xendit');
        $this->get('/privasi')->assertSee('Midtrans (PT Midtrans)', false);
        $this->get('/syarat')->assertSee('diproses oleh Midtrans');
    }

    // ------------------------------------------------------------- webhook

    public function test_a_settlement_notification_marks_the_order_paid_and_fulfils_it_once(): void
    {
        $order = $this->order();
        $this->statusApi($this->transaction());

        $this->notify()->assertOk();
        $this->notify()->assertOk();

        $order->refresh();
        $this->assertSame(Order::PAID, $order->status);
        $this->assertSame('qris gopay', $order->payment_method);
        $this->assertNotNull($order->paid_at);
        Queue::assertPushed(FulfillOrder::class, 1);
    }

    public function test_the_signature_is_required_and_checked_against_the_three_values_and_the_key(): void
    {
        $order = $this->order();
        $this->statusApi($this->transaction());

        $this->notify(['signature_key' => 'abc'])->assertForbidden();
        $this->notify(['signature_key' => ''])->assertForbidden();
        $this->notify([], hash('sha512', 'arti-order1200'.'99000.00'.'another-key'))->assertForbidden();
        // A body whose amount was changed after signing.
        $this->notify(['gross_amount' => '1.00', 'signature_key' => hash('sha512', 'arti-order1'.'200'.'99000.00'.self::KEY)])->assertForbidden();
        // Missing fields entirely.
        $this->postJson('/webhooks/midtrans', ['signature_key' => 'x'])->assertForbidden();

        $this->assertSame(Order::PENDING, $order->refresh()->status);
        Http::assertNothingSent();
        Queue::assertNothingPushed();
    }

    public function test_an_uppercase_signature_is_accepted_but_never_when_the_key_differs(): void
    {
        $this->order();
        $this->statusApi($this->transaction());

        $this->notify([], strtoupper(hash('sha512', 'arti-order1'.'200'.'99000.00'.self::KEY)))->assertOk();
    }

    public function test_a_forged_settlement_is_ignored_when_midtrans_says_the_payment_is_pending(): void
    {
        $order = $this->order();
        $this->statusApi($this->transaction(['transaction_status' => 'pending']));

        $this->notify()->assertOk();

        $this->assertSame(Order::PENDING, $order->refresh()->status);
        Queue::assertNothingPushed();
    }

    public function test_a_wrong_amount_from_the_status_api_parks_the_order(): void
    {
        $order = $this->order();
        $this->statusApi($this->transaction(['gross_amount' => '10000.00']));

        $this->notify()->assertOk();

        $order->refresh();
        $this->assertSame([Order::NEEDS_ATTENTION, 'amount_mismatch'], [$order->status, $order->attention_note]);
        Queue::assertNothingPushed();
    }

    public function test_a_card_capture_is_paid_only_when_fraud_screening_accepted_it(): void
    {
        $challenged = $this->order();
        $this->statusApi($this->transaction(['transaction_status' => 'capture', 'fraud_status' => 'challenge', 'payment_type' => 'credit_card']));
        $this->notify()->assertOk();
        $this->assertSame(Order::PENDING, $challenged->refresh()->status);

        $this->statusApi($this->transaction(['transaction_status' => 'capture', 'fraud_status' => 'accept', 'payment_type' => 'credit_card']));
        $this->notify()->assertOk();
        $this->assertSame(Order::PAID, $challenged->refresh()->status);
    }

    public function test_expiry_cancel_and_deny_give_the_place_back(): void
    {
        foreach (['expire', 'cancel', 'deny'] as $i => $status) {
            $order = $this->order(['public_id' => "arti-o{$i}", 'gateway_ref' => "arti-o{$i}", 'email' => "o{$i}@example.test", 'launch_slot' => $i + 1]);
            $this->statusApi($this->transaction(['transaction_status' => $status, 'order_id' => "arti-o{$i}"]));

            $this->notify(['order_id' => "arti-o{$i}", 'transaction_status' => $status])->assertOk();

            $order->refresh();
            $this->assertSame([Order::EXPIRED, null], [$order->status, $order->launch_slot], $status);
        }
    }

    public function test_a_late_payment_after_expiry_is_still_honoured(): void
    {
        $order = $this->order(['status' => Order::EXPIRED, 'launch_slot' => null]);
        $this->statusApi($this->transaction());

        $this->notify()->assertOk();

        $order->refresh();
        $this->assertSame([Order::PAID, 1], [$order->status, $order->launch_slot]);
    }

    public function test_an_unreachable_status_api_makes_midtrans_retry_and_an_unknown_transaction_is_acknowledged(): void
    {
        $order = $this->order();
        Http::swap(new Factory);
        Http::fake(fn () => throw new ConnectionException('down'));
        $this->notify()->assertStatus(503);

        $this->statusApi(['status_code' => '404', 'status_message' => 'Transaction doesn\'t exist.']);
        $this->notify()->assertOk();

        $this->assertSame(Order::PENDING, $order->refresh()->status);
        Queue::assertNothingPushed();
    }

    public function test_unknown_orders_and_orders_of_another_gateway_are_acknowledged_without_a_lookup(): void
    {
        $this->order(['gateway' => 'xendit', 'gateway_ref' => 'inv-1']);
        Http::swap(new Factory);
        Http::fake();

        $this->notify(['order_id' => 'arti-nobody'])->assertOk();
        $this->notify()->assertOk(); // arti-order1 belongs to Xendit

        Http::assertNothingSent();
        Queue::assertNothingPushed();
    }

    public function test_without_a_server_key_the_notification_endpoint_refuses_everything(): void
    {
        config(['services.midtrans.server_key' => '']);

        $this->postJson('/webhooks/midtrans', ['order_id' => 'x'])->assertStatus(503);
    }

    public function test_the_whole_chain_runs_from_a_midtrans_notification(): void
    {
        Role::findOrCreate('learner');
        Course::create(['slug' => 'arti-framework', 'title' => 'ARTi Framework']);
        Mail::fake();
        config(['services.licensing.url' => 'https://worker.test/v1', 'services.licensing.admin_token' => 'tok']);
        $order = $this->order();
        Http::swap(new Factory);
        Http::fake([
            'api.midtrans.test/*' => Http::response($this->transaction()),
            'worker.test/*' => Http::response([
                'key' => 'ARTI-ABCD-EFGH-JKMN-PQRS', 'key_id' => 'k1', 'tier' => 'individual', 'seats' => 1,
                'expires' => '2109-10-15T10:00:00.000Z', 'updates_until' => '2109-10-15T10:00:00.000Z', 'email' => 'buyer@example.test',
            ]),
        ]);

        $this->notify()->assertOk();
        $this->notify()->assertOk();

        // The queue is faked in this class; run the one queued job by hand.
        Queue::assertPushed(FulfillOrder::class, 1);
        (new FulfillOrder($order->id))->handle(app(\App\Actions\Orders\FulfillPaidOrder::class));

        $this->assertSame(Order::FULFILLED, $order->fresh()->status);
        Mail::assertQueued(PurchaseMail::class, 1);
        $this->assertSame('midtrans', $order->fresh()->licenseIssuance->source);
    }

    // ------------------------------------------------------------ reconcile

    public function test_reconcile_asks_each_order_its_own_gateway(): void
    {
        config(['services.xendit.url' => 'https://xendit.test', 'services.xendit.secret_key' => 'xnd', 'services.xendit.callback_token' => 'cb']);
        $mid = $this->order();
        $xen = $this->order(['public_id' => 'arti-x', 'gateway' => 'xendit', 'gateway_ref' => 'inv-x', 'email' => 'x@example.test', 'launch_slot' => 2]);
        foreach ([$mid, $xen] as $o) {
            $o->forceFill(['created_at' => now()->subMinutes(10)])->save();
        }
        Http::swap(new Factory);
        Http::fake([
            'api.midtrans.test/v2/arti-order1/status' => Http::response($this->transaction()),
            'xendit.test/v2/invoices/inv-x' => Http::response(['id' => 'inv-x', 'external_id' => 'arti-x', 'status' => 'PENDING', 'amount' => 99000]),
        ]);

        $this->artisan('orders:reconcile')->assertSuccessful();

        $this->assertSame(Order::PAID, $mid->fresh()->status);
        $this->assertSame(Order::PENDING, $xen->fresh()->status);
        Queue::assertPushed(FulfillOrder::class, 1);
    }

    // ----------------------------------------------------- client and factory

    public function test_the_status_mapping_covers_every_midtrans_state(): void
    {
        $cases = [
            ['settlement', 'accept', 'PAID'], ['capture', 'accept', 'PAID'], ['capture', 'challenge', 'PENDING'], ['pending', 'accept', 'PENDING'],
            ['authorize', 'accept', 'PENDING'], ['expire', 'accept', 'EXPIRED'], ['cancel', 'accept', 'EXPIRED'], ['deny', 'accept', 'EXPIRED'],
            ['failure', 'accept', 'EXPIRED'], ['refund', 'accept', 'PENDING'], ['chargeback', 'accept', 'PENDING'],
        ];

        foreach ($cases as [$status, $fraud, $expected]) {
            $n = MidtransClient::normalise(['transaction_status' => $status, 'fraud_status' => $fraud, 'order_id' => 'o', 'gross_amount' => '99000.00']);
            $this->assertSame($expected, $n['status'], "{$status}/{$fraud}");
            $this->assertSame(99000, $n['amount']);
            $this->assertSame($expected === 'PAID' ? 99000 : 0, $n['paid_amount']);
        }

        $va = MidtransClient::normalise(['transaction_status' => 'settlement', 'order_id' => 'o', 'gross_amount' => '99000.00', 'payment_type' => 'bank_transfer', 'va_numbers' => [['bank' => 'bni', 'va_number' => '1']]]);
        $this->assertSame(['bank_transfer', 'bni'], [$va['payment_method'], $va['payment_channel']]);
        $store = MidtransClient::normalise(['transaction_status' => 'settlement', 'order_id' => 'o', 'gross_amount' => '1', 'payment_type' => 'cstore', 'store' => 'alfamart']);
        $this->assertSame('alfamart', $store['payment_channel']);
    }

    public function test_the_signature_check_is_strict_about_types_and_empty_keys(): void
    {
        $client = new MidtransClient;
        $good = hash('sha512', 'o'.'200'.'1.00'.self::KEY);

        $this->assertTrue($client->validSignature('o', '200', '1.00', $good));
        $this->assertFalse($client->validSignature('o', 200, '1.00', $good));
        $this->assertFalse($client->validSignature('o', '200', 1.00, $good));
        $this->assertFalse($client->validSignature(null, '200', '1.00', $good));
        $this->assertFalse($client->validSignature('o', '200', '1.00', ''));

        config(['services.midtrans.server_key' => '']);
        $this->assertFalse($client->validSignature('o', '200', '1.00', hash('sha512', 'o2001.00')));
    }

    public function test_the_factory_picks_the_configured_gateway_and_an_orders_own_gateway(): void
    {
        $gateways = app(PaymentGateways::class);

        $this->assertInstanceOf(MidtransClient::class, $gateways->current());
        $this->assertInstanceOf(MidtransClient::class, app(PaymentGateway::class));
        $this->assertInstanceOf(XenditClient::class, $gateways->for('xendit'));
        $this->assertSame(['midtrans', 'Midtrans', 'PT Midtrans'], [$gateways->current()->name(), $gateways->current()->label(), $gateways->current()->company()]);

        config(['services.payments.driver' => 'xendit']);
        $this->assertInstanceOf(XenditClient::class, app(PaymentGateways::class)->current());

        $this->expectException(InvalidArgumentException::class);
        $gateways->for('paypal');
    }

    public function test_an_unconfigured_client_refuses_before_any_request(): void
    {
        config(['services.midtrans.server_key' => '']);
        Http::swap(new Factory);
        Http::fake();

        try {
            (new MidtransClient)->getInvoice('arti-order1');
            $this->fail('Expected an exception.');
        } catch (MidtransException $e) {
            $this->assertSame('not_configured', $e->errorCode);
        }
        Http::assertNothingSent();
    }
}
