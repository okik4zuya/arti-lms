<?php

namespace Tests\Feature;

use App\Jobs\FulfillOrder;
use App\Models\Order;
use App\Services\Payments\MayarClient;
use App\Services\Payments\PaymentGateways;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class MayarGatewayTest extends TestCase
{
    use RefreshDatabase;

    private const SECRET = 'hook-secret-123';

    private const REF = '7c9d2e1f-3a4b-4c5d-8e6f-1a2b3c4d5e6f';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.payments.driver' => 'mayar',
            'services.mayar.api_key' => 'mayar-key',
            'services.mayar.api_url' => 'https://api.mayar.test/hl/v2',
            'services.mayar.webhook_secret' => self::SECRET,
            'services.mayar.webhook_token' => '',
            'site.offer.launch_sold' => null,
        ]);
        Queue::fake();
    }

    private function order(array $over = []): Order
    {
        return Order::create($over + [
            'public_id' => 'arti-order1', 'name' => 'Buyer', 'email' => 'buyer@example.test', 'amount' => 99000, 'price_tier' => 'launch',
            'launch_slot' => 1, 'status' => Order::PENDING, 'gateway' => 'mayar', 'gateway_ref' => self::REF, 'expires_at' => now()->addHour(),
        ]);
    }

    private function paymentApi(array $data = [], int $http = 200): void
    {
        Http::swap(new Factory);
        Http::fake(['api.mayar.test/hl/v2/payments/*' => Http::response(['statusCode' => 200, 'messages' => 'success', 'data' => $data + [
            'id' => self::REF, 'status' => 'paid', 'amount' => 99000, 'transactions' => [],
        ]], $http)]);
    }

    private function hook(array $body = [], string $secret = self::SECRET)
    {
        return $this->postJson('/webhooks/mayar/'.$secret, $body + [
            'event' => 'payment.received',
            'data' => ['id' => 'tx-1', 'transactionId' => 'tx-1', 'productId' => self::REF, 'status' => 'SUCCESS', 'transactionStatus' => 'paid', 'amount' => 99000],
        ]);
    }

    // ------------------------------------------------------------ checkout

    public function test_checkout_creates_a_payment_request_for_the_exact_amount_and_redirects_to_its_link(): void
    {
        Http::swap(new Factory);
        Http::fake(['api.mayar.test/hl/v2/payments/create' => Http::response(['statusCode' => 200, 'data' => ['id' => self::REF, 'transactionId' => 'tx', 'link' => 'https://x.myr.id/pay/abc', 'redirectUrl' => 'x']])]);

        $this->post('/beli', ['nama' => 'Budi', 'email' => 'Budi@Example.test', 'whatsapp' => '0812-3456-7890', 'metode' => 'qris', 'setuju' => '1'])
            ->assertRedirect('https://x.myr.id/pay/abc');

        $order = Order::firstOrFail();
        $this->assertSame(['mayar', self::REF, 99000], [$order->gateway, $order->gateway_ref, $order->amount]);

        Http::assertSent(function (Request $r) use ($order) {
            return $r->url() === 'https://api.mayar.test/hl/v2/payments/create'
                && $r->hasHeader('Authorization', 'Bearer mayar-key')
                && $r['amount'] === 99000
                && $r['email'] === 'budi@example.test'
                && $r['mobile'] === '6281234567890'
                && str_contains($r['description'], $order->public_id)
                && str_contains($r['redirectUrl'], '/beli/selesai/'.$order->public_id)
                && str_ends_with($r['expiredAt'], 'Z');
        });
    }

    public function test_when_mayar_refuses_the_place_is_given_back(): void
    {
        Http::swap(new Factory);
        Http::fake(['api.mayar.test/*' => Http::response(['statusCode' => 400, 'messages' => 'redirectUrl not supported'], 400)]);

        $this->post('/beli', ['nama' => 'Budi', 'email' => 'budi@example.test', 'metode' => 'va', 'setuju' => '1'])->assertSessionHasErrors('pembayaran');

        $order = Order::firstOrFail();
        $this->assertSame([Order::FAILED, null], [$order->status, $order->launch_slot]);
    }

    // ------------------------------------------------------------- webhook

    public function test_a_payment_received_webhook_marks_the_order_paid_and_fulfils_it_once(): void
    {
        $order = $this->order();
        $this->paymentApi();

        $this->hook()->assertOk();
        $this->hook()->assertOk();

        $order->refresh();
        $this->assertSame(Order::PAID, $order->status);
        $this->assertNotNull($order->paid_at);
        Queue::assertPushed(FulfillOrder::class, 1);
    }

    public function test_the_secret_in_the_url_is_required(): void
    {
        $order = $this->order();
        $this->paymentApi();

        $this->hook([], 'wrong')->assertForbidden();
        config(['services.mayar.webhook_secret' => '']);
        $this->hook([], '')->assertNotFound();
        $this->hook([], 'x')->assertForbidden();

        $this->assertSame(Order::PENDING, $order->refresh()->status);
        Http::assertNothingSent();
        Queue::assertNothingPushed();
    }

    public function test_the_webhook_token_is_required_when_set_and_found_in_any_header_or_the_body(): void
    {
        config(['services.mayar.webhook_token' => 'tok-abc']);
        $order = $this->order();
        $this->paymentApi();

        $this->hook()->assertForbidden();
        $this->withHeaders(['X-Callback-Token' => 'wrong'])->postJson('/webhooks/mayar/'.self::SECRET, [])->assertForbidden();
        $this->assertSame(Order::PENDING, $order->refresh()->status);

        $this->withHeaders(['Authorization' => 'Bearer tok-abc'])->postJson('/webhooks/mayar/'.self::SECRET, [])->assertOk();
        $this->withHeaders(['X-Whatever' => 'tok-abc'])->postJson('/webhooks/mayar/'.self::SECRET, [])->assertOk();
        $this->postJson('/webhooks/mayar/'.self::SECRET, ['token' => 'tok-abc'])->assertOk();

        $this->withHeaders(['X-Callback-Token' => 'tok-abc'])->postJson('/webhooks/mayar/'.self::SECRET, [
            'event' => 'payment.received', 'data' => ['productId' => self::REF],
        ])->assertOk();
        $this->assertSame(Order::PAID, $order->refresh()->status);
    }

    public function test_a_forged_webhook_is_ignored_when_mayar_says_the_request_is_unpaid(): void
    {
        $order = $this->order();
        $this->paymentApi(['status' => 'unpaid']);

        $this->hook()->assertOk();

        $this->assertSame(Order::PENDING, $order->refresh()->status);
        Queue::assertNothingPushed();
    }

    public function test_a_wrong_amount_from_the_payment_detail_parks_the_order(): void
    {
        $order = $this->order();
        $this->paymentApi(['amount' => 10000]);

        $this->hook()->assertOk();

        $order->refresh();
        $this->assertSame([Order::NEEDS_ATTENTION, 'amount_mismatch'], [$order->status, $order->attention_note]);
        Queue::assertNothingPushed();
    }

    public function test_a_closed_request_expires_the_order_and_gives_the_place_back(): void
    {
        $order = $this->order();
        $this->paymentApi(['status' => 'closed']);

        $this->hook()->assertOk();

        $order->refresh();
        $this->assertSame([Order::EXPIRED, null], [$order->status, $order->launch_slot]);
    }

    public function test_the_order_is_found_by_the_payment_request_id_in_product_id_and_the_method_is_taken_from_the_body(): void
    {
        $order = $this->order();
        $this->paymentApi();

        // The transaction id is not the payment request id: it never matches an order.
        $this->hook(['data' => ['id' => self::REF, 'transactionId' => self::REF]])->assertOk()->assertJson(['status' => 'ignored']);
        $this->assertSame(Order::PENDING, $order->refresh()->status);

        $this->hook(['data' => ['id' => 'tx-1', 'productId' => self::REF, 'paymentMethod' => 'QRIS']])->assertOk();
        $order->refresh();
        $this->assertSame([Order::PAID, 'QRIS'], [$order->status, $order->payment_method]);

        $this->hook(['data' => ['productId' => 'someone-else']])->assertOk()->assertJson(['status' => 'ignored']);
        $this->hook(['event' => 'payment.reminder'])->assertOk()->assertJson(['status' => 'ignored']);
        $this->postJson('/webhooks/mayar/'.self::SECRET, [])->assertOk()->assertJson(['status' => 'ignored']);
    }

    public function test_an_unreachable_mayar_makes_the_webhook_ask_for_a_retry(): void
    {
        $this->order();
        Http::swap(new Factory);
        Http::fake(['api.mayar.test/*' => Http::response([], 503)]);

        $this->hook()->assertStatus(503);
    }

    // ------------------------------------------------------------ client

    public function test_the_driver_is_registered_and_status_values_are_normalised(): void
    {
        $this->assertInstanceOf(MayarClient::class, app(PaymentGateways::class)->current());
        $this->assertInstanceOf(MayarClient::class, app(PaymentGateways::class)->for('mayar'));

        $this->assertSame('PAID', MayarClient::normalise(['status' => 'paid', 'amount' => 99000], 'r', 'o')['status']);
        $this->assertSame('PENDING', MayarClient::normalise(['status' => 'unpaid', 'amount' => 99000], 'r', 'o')['status']);
        $this->assertSame('EXPIRED', MayarClient::normalise(['status' => 'closed', 'amount' => 99000], 'r', 'o')['status']);
        $this->assertSame(0, MayarClient::normalise(['status' => 'unpaid', 'amount' => 99000], 'r', 'o')['paid_amount']);
    }

    public function test_a_reference_we_never_issued_is_not_found_without_calling_mayar(): void
    {
        Http::swap(new Factory);
        Http::fake();

        try {
            app(MayarClient::class)->getInvoice('unknown');
            $this->fail('expected an exception');
        } catch (\App\Services\Payments\PaymentGatewayException $e) {
            $this->assertTrue($e->isNotFound());
        }

        Http::assertNothingSent();
    }
}
