<?php

namespace Tests\Feature;

use App\Actions\Orders\FulfillPaidOrder;
use App\Jobs\SendOrderWhatsApp;
use App\Mail\PurchaseMail;
use App\Models\Course;
use App\Models\Order;
use App\Services\WhatsApp\LogSender;
use App\Services\WhatsApp\WhatsAppException;
use App\Services\WhatsApp\WhatsAppSender;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RecordingSender implements WhatsAppSender
{
    /** @var list<array{0:string,1:string}> */
    public array $sent = [];

    public bool $fail = false;

    public function enabled(): bool
    {
        return true;
    }

    public function send(string $number, string $message): void
    {
        if ($this->fail) {
            throw new WhatsAppException('provider said no: '.$message);
        }
        $this->sent[] = [$number, $message];
    }
}

class OrderWhatsAppTest extends TestCase
{
    use RefreshDatabase;

    private const KEY = 'ARTI-ABCD-EFGH-JKMN-PQRS';

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.licensing.url' => 'https://worker.test/v1', 'services.licensing.admin_token' => 'tok']);
        Role::findOrCreate('learner');
        Course::create(['slug' => 'arti-framework', 'title' => 'ARTi Framework']);
        Mail::fake();
        Http::swap(new Factory);
        Http::fake(['worker.test/*' => Http::response([
            'key' => self::KEY, 'key_id' => 'k1', 'tier' => 'individual', 'seats' => 1,
            'expires' => '2109-10-15T10:00:00.000Z', 'updates_until' => '2109-10-15T10:00:00.000Z', 'email' => 'buyer@example.test',
        ])]);
    }

    private function sender(bool $fail = false): RecordingSender
    {
        $sender = new RecordingSender;
        $sender->fail = $fail;
        $this->app->instance(WhatsAppSender::class, $sender);

        return $sender;
    }

    private function paidOrder(array $over = []): Order
    {
        return Order::create($over + [
            'public_id' => 'arti-order1', 'name' => 'Budi Pembeli', 'email' => 'buyer@example.test', 'amount' => 99000,
            'price_tier' => 'launch', 'status' => Order::PAID, 'gateway_ref' => 'inv-1', 'whatsapp' => '081234567890',
        ]);
    }

    public function test_the_default_driver_sends_nothing_and_the_order_is_unaffected(): void
    {
        $order = $this->paidOrder();

        app(FulfillPaidOrder::class)->handle($order);

        $order->refresh();
        $this->assertSame(Order::FULFILLED, $order->status);
        $this->assertNull($order->whatsapp_sent_at);
        $this->assertTrue($order->events()->where('event', 'whatsapp_skipped')->exists());
    }

    public function test_the_message_has_account_link_key_and_download_and_no_password(): void
    {
        $sender = $this->sender();
        config(['services.licensing.installer_url' => 'https://cdn.example.test/installers/ArtiFrameworkSetup-0.1.9.exe']);
        $order = $this->paidOrder();

        app(FulfillPaidOrder::class)->handle($order);

        $this->assertCount(1, $sender->sent);
        [$number, $text] = $sender->sent[0];
        $this->assertSame('6281234567890', $number);
        $this->assertStringContainsString('Halo Budi Pembeli', $text);
        $this->assertStringContainsString('buyer@example.test', $text);
        $this->assertStringContainsString(self::KEY, $text);
        $this->assertStringContainsString('b=welcome', $text);
        $this->assertStringContainsString('berlaku selamanya', $text);
        $this->assertStringContainsString('/download-installer', $text);
        $this->assertStringNotContainsString('ArtiFrameworkSetup', $text);
        $this->assertStringNotContainsString('arti-license', $text);
        $this->assertStringNotContainsString('terminal', $text);
        $this->assertStringContainsString('Password tidak pernah dikirim', $text);
        $this->assertStringNotContainsString('sementara', $text);

        // The link in the message is the same kind of link the mail got, and it works.
        preg_match('#https?://\S+atur-sandi\S+#', $text, $m);
        $this->get($m[0])->assertOk()->assertSee('Atur password akun belajar');

        $order->refresh();
        $this->assertNotNull($order->whatsapp_sent_at);
        $this->assertSame(Order::FULFILLED, $order->status);
    }

    public function test_the_message_is_sent_once_however_often_the_stage_runs(): void
    {
        $sender = $this->sender();
        $order = $this->paidOrder();
        app(FulfillPaidOrder::class)->handle($order);

        (new SendOrderWhatsApp($order->id, null))->handle($sender);
        (new SendOrderWhatsApp($order->id, null))->handle($sender);

        $this->assertCount(1, $sender->sent);
    }

    public function test_a_failing_provider_does_not_fail_the_order_and_allows_a_retry(): void
    {
        $sender = $this->sender(fail: true);
        $order = $this->paidOrder();

        app(FulfillPaidOrder::class)->handle($order);
        $order->refresh();

        // The failure surfaces in the WhatsApp job only; the order is done and the mail went out.
        $this->assertSame(Order::FULFILLED, $order->status);
        Mail::assertQueued(PurchaseMail::class, 1);
        $this->assertNull($order->whatsapp_sent_at);

        $job = new SendOrderWhatsApp($order->id, null);
        $job->failed(new WhatsAppException('provider said no: '.self::KEY));
        $event = $order->events()->where('event', 'whatsapp_failed')->first();
        $this->assertSame('WhatsAppException', $event->detail);
        $this->assertStringNotContainsString(self::KEY, (string) $event->detail);

        $sender->fail = false;
        $job->handle($sender);
        $this->assertCount(1, $sender->sent);
    }

    public function test_an_order_without_a_whatsapp_number_queues_no_message(): void
    {
        Queue::fake([SendOrderWhatsApp::class]);
        $order = $this->paidOrder(['whatsapp' => null]);

        app(FulfillPaidOrder::class)->handle($order);

        Queue::assertNotPushed(SendOrderWhatsApp::class);
        $this->assertSame(Order::FULFILLED, $order->fresh()->status);
    }

    public function test_a_buyer_with_an_old_account_gets_no_link_in_the_message(): void
    {
        $sender = $this->sender();
        $user = \App\Models\User::factory()->create(['email' => 'buyer@example.test']);
        $user->assignRole('learner');

        app(FulfillPaidOrder::class)->handle($this->paidOrder());

        $this->assertStringNotContainsString('atur-sandi', $sender->sent[0][1]);
        $this->assertStringContainsString('password lama Anda tetap berlaku', $sender->sent[0][1]);
    }

    public function test_the_log_driver_never_writes_the_message(): void
    {
        Log::spy();
        (new LogSender)->send('6281234567890', 'kunci '.self::KEY);

        Log::shouldHaveReceived('info')->once()->withArgs(function (string $message, array $context) {
            return ! str_contains($message.json_encode($context), self::KEY)
                && $context['to'] === '***7890' && $context['length'] === mb_strlen('kunci '.self::KEY);
        });
    }
}
