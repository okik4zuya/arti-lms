<?php

namespace Tests\Feature;

use App\Jobs\FulfillOrder;
use App\Models\Order;
use App\Services\Payments\XenditClient;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class PaymentWebhookTest extends TestCase
{
    use RefreshDatabase;

    private const TOKEN = 'cb-token-test';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.payments.driver' => 'xendit',
            'services.xendit.url' => 'https://xendit.test',
            'services.xendit.secret_key' => 'xnd_development_test',
            'services.xendit.callback_token' => self::TOKEN,
        ]);
        Queue::fake();
    }

    private function order(array $over = []): Order
    {
        return Order::create($over + [
            'public_id' => 'arti-order1', 'name' => 'Buyer', 'email' => 'buyer@example.test', 'amount' => 99000,
            'price_tier' => 'launch', 'status' => Order::PENDING, 'gateway_ref' => 'inv-1',
        ]);
    }

    private function invoice(array $over = []): array
    {
        return $over + [
            'id' => 'inv-1', 'external_id' => 'arti-order1', 'status' => 'PAID', 'amount' => 99000,
            'paid_amount' => 99000, 'payment_method' => 'QR_CODE', 'payment_channel' => 'QRIS',
        ];
    }

    private function notify(array $body = ['id' => 'inv-1', 'external_id' => 'arti-order1', 'status' => 'PAID'], ?string $token = self::TOKEN)
    {
        return $this->postJson('/webhooks/pembayaran', $body, $token === null ? [] : ['x-callback-token' => $token]);
    }

    private function xendit(array $invoice): void
    {
        // A new factory each time: stubs of an earlier call would otherwise win.
        Http::swap(new Factory);
        Http::fake(['xendit.test/v2/invoices/*' => Http::response($invoice)]);
    }

    public function test_a_paid_callback_marks_the_order_paid_and_dispatches_fulfilment_once(): void
    {
        $order = $this->order();
        $this->xendit($this->invoice());

        $this->notify()->assertOk();

        $order->refresh();
        $this->assertSame(Order::PAID, $order->status);
        $this->assertNotNull($order->paid_at);
        $this->assertSame('QR_CODE QRIS', $order->payment_method);
        $this->assertSame('paid', $order->events()->first()->event);
        Queue::assertPushed(FulfillOrder::class, 1);
        Queue::assertPushed(FulfillOrder::class, fn ($job) => $job->orderId === $order->id);
    }

    public function test_the_same_callback_delivered_again_fulfils_nothing_twice(): void
    {
        $this->order();
        $this->xendit($this->invoice());

        $this->notify()->assertOk();
        $this->notify()->assertOk();
        $this->notify(['id' => 'inv-1', 'external_id' => 'arti-order1', 'status' => 'SETTLED'])->assertOk();

        Queue::assertPushed(FulfillOrder::class, 1);
        $this->assertSame(1, Order::first()->events()->where('event', 'paid')->count());
    }

    public function test_the_callback_token_is_required_and_compared_exactly(): void
    {
        $order = $this->order();
        $this->xendit($this->invoice());

        $this->notify(token: null)->assertForbidden();
        $this->notify(token: 'wrong')->assertForbidden();
        $this->notify(token: self::TOKEN.' ')->assertForbidden();

        $this->assertSame(Order::PENDING, $order->refresh()->status);
        Http::assertNothingSent();
        Queue::assertNothingPushed();
    }

    public function test_without_configuration_the_webhook_refuses_everything(): void
    {
        config(['services.xendit.callback_token' => '']);
        $this->order();

        $this->notify(token: '')->assertStatus(503);
        $this->notify(token: null)->assertStatus(503);
    }

    public function test_a_forged_paid_body_is_ignored_when_xendit_says_the_invoice_is_not_paid(): void
    {
        $order = $this->order();
        $this->xendit($this->invoice(['status' => 'PENDING']));

        $this->notify()->assertOk();

        $this->assertSame(Order::PENDING, $order->refresh()->status);
        Queue::assertNothingPushed();
    }

    public function test_a_wrong_amount_sends_the_order_to_an_admin_instead_of_fulfilling_it(): void
    {
        $order = $this->order();
        $this->xendit($this->invoice(['amount' => 99000, 'paid_amount' => 10000]));

        $this->notify()->assertOk();

        $order->refresh();
        $this->assertSame(Order::NEEDS_ATTENTION, $order->status);
        $this->assertSame('amount_mismatch', $order->attention_note);
        $this->assertSame('amount_mismatch', $order->events()->first()->event);
        Queue::assertNothingPushed();
    }

    public function test_an_invoice_that_belongs_to_another_order_is_ignored(): void
    {
        $order = $this->order();
        $this->xendit($this->invoice(['external_id' => 'arti-other']));
        $this->notify()->assertOk();

        $this->xendit($this->invoice(['id' => 'inv-other']));
        $this->notify()->assertOk();

        $this->assertSame(Order::PENDING, $order->refresh()->status);
        Queue::assertNothingPushed();
    }

    public function test_an_unknown_order_is_acknowledged_without_calling_xendit(): void
    {
        Http::fake();

        $this->notify(['id' => 'inv-9', 'external_id' => 'arti-nobody', 'status' => 'PAID'])->assertOk();
        $this->notify(['status' => 'PAID'])->assertOk();

        Http::assertNothingSent();
        Queue::assertNothingPushed();
    }

    public function test_an_unreachable_xendit_makes_the_callback_retry_later(): void
    {
        $order = $this->order();
        Http::fake(fn () => throw new ConnectionException('down'));

        $this->notify()->assertStatus(503);

        $this->assertSame(Order::PENDING, $order->refresh()->status);
        Queue::assertNothingPushed();
    }

    public function test_an_invoice_xendit_does_not_know_is_acknowledged(): void
    {
        $this->order();
        Http::fake(['xendit.test/*' => Http::response(['error_code' => 'INVOICE_NOT_FOUND_ERROR'], 404)]);

        $this->notify()->assertOk();

        Queue::assertNothingPushed();
    }

    public function test_expiry_then_a_late_payment_still_fulfils_and_a_paid_order_never_goes_back(): void
    {
        $order = $this->order();

        $this->xendit($this->invoice(['status' => 'EXPIRED', 'paid_amount' => 0]));
        $this->notify(['id' => 'inv-1', 'external_id' => 'arti-order1', 'status' => 'EXPIRED'])->assertOk();
        $this->assertSame(Order::EXPIRED, $order->refresh()->status);

        $this->xendit($this->invoice());
        $this->notify()->assertOk();
        $this->assertSame(Order::PAID, $order->refresh()->status);
        Queue::assertPushed(FulfillOrder::class, 1);

        $this->xendit($this->invoice(['status' => 'EXPIRED']));
        $this->notify(['id' => 'inv-1', 'external_id' => 'arti-order1', 'status' => 'EXPIRED'])->assertOk();
        $this->assertSame(Order::PAID, $order->refresh()->status);
    }

    public function test_an_order_without_a_stored_invoice_id_adopts_the_one_xendit_confirms(): void
    {
        $order = $this->order(['gateway_ref' => null]);
        $this->xendit($this->invoice());

        $this->notify()->assertOk();

        $this->assertSame('inv-1', $order->refresh()->gateway_ref);
    }

    public function test_two_orders_can_never_share_a_launch_slot(): void
    {
        $this->order(['launch_slot' => 100, 'gateway_ref' => 'a']);

        $this->expectException(UniqueConstraintViolationException::class);
        $this->order(['public_id' => 'arti-order2', 'launch_slot' => 100, 'gateway_ref' => 'b']);
    }

    public function test_create_invoice_sends_the_order_id_and_exact_amount_with_the_secret_as_basic_auth(): void
    {
        $order = $this->order(['gateway_ref' => null, 'whatsapp' => '6281234567890']);
        Http::fake(['xendit.test/v2/invoices' => Http::response(['id' => 'inv-1', 'invoice_url' => 'https://checkout.xendit.test/x', 'status' => 'PENDING'])]);

        $invoice = app(XenditClient::class)->createInvoice($order, 'https://arti.test/ok', 'https://arti.test/gagal');

        $this->assertSame('inv-1', $invoice['id']);
        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://xendit.test/v2/invoices'
                && $request->hasHeader('Authorization', 'Basic '.base64_encode('xnd_development_test:'))
                && $request['external_id'] === 'arti-order1'
                && $request['amount'] === 99000
                && $request['currency'] === 'IDR'
                && $request['success_redirect_url'] === 'https://arti.test/ok';
        });
    }

    public function test_the_webhook_route_needs_no_csrf_token(): void
    {
        $this->post('/webhooks/pembayaran', [], ['x-callback-token' => 'wrong'])->assertForbidden();
    }
}
