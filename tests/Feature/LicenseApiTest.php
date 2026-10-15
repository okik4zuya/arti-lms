<?php

namespace Tests\Feature;

use App\Services\Licensing\LicenseApi;
use App\Services\Licensing\LicenseApiException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LicenseApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['services.licensing.url' => 'https://worker.test/v1', 'services.licensing.admin_token' => 'secret-token']);
    }

    public function test_issue_posts_to_the_admin_endpoint_with_the_bearer_token(): void
    {
        Http::fake(['worker.test/*' => Http::response(['key' => 'ARTI-AAAA', 'key_id' => 'k1'])]);

        $result = app(LicenseApi::class)->issue(['email' => 'a@example.test', 'order' => 'WA-1']);

        $this->assertSame('k1', $result['key_id']);
        Http::assertSent(fn (Request $r) => $r->url() === 'https://worker.test/v1/admin/issue'
            && $r->hasHeader('Authorization', 'Bearer secret-token')
            && $r['order'] === 'WA-1');
    }

    public function test_show_sends_the_key_id_as_a_query_parameter(): void
    {
        Http::fake(['worker.test/*' => Http::response(['license' => [], 'activations' => []])]);

        app(LicenseApi::class)->show('k1');

        Http::assertSent(fn (Request $r) => $r->method() === 'GET' && str_contains($r->url(), '/v1/admin/show?key_id=k1'));
    }

    public function test_verify_key_is_public_and_carries_no_admin_token(): void
    {
        Http::fake(['worker.test/*' => Http::response(['key_id' => 'k1'])]);

        app(LicenseApi::class)->verifyKey('ARTI-AAAA');

        Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/v1/activations') && ! $r->hasHeader('Authorization'));
    }

    public function test_order_exists_carries_the_key_id(): void
    {
        Http::fake(['worker.test/*' => Http::response(['code' => 'order_exists', 'message' => 'order_ref already issued', 'key_id' => 'k9'], 409)]);

        try {
            app(LicenseApi::class)->issue([]);
            $this->fail('expected an exception');
        } catch (LicenseApiException $e) {
            $this->assertTrue($e->isOrderExists());
            $this->assertSame('k9', $e->extra['key_id']);
            $this->assertFalse($e->isUnreachable());
        }
    }

    public function test_not_found_and_auth_errors_are_mapped(): void
    {
        Http::fake(['worker.test/*' => Http::sequence()
            ->push(['code' => 'not_found', 'message' => 'unknown key_id'], 404)
            ->push(['code' => 'not_entitled', 'message' => 'admin token required'], 401)]);

        try {
            app(LicenseApi::class)->show('nope');
        } catch (LicenseApiException $e) {
            $this->assertTrue($e->isNotFound());
        }

        try {
            app(LicenseApi::class)->show('nope');
        } catch (LicenseApiException $e) {
            $this->assertSame('not_entitled', $e->errorCode);
            $this->assertFalse($e->isUnreachable());
        }
    }

    public function test_connection_failures_and_server_errors_count_as_unreachable(): void
    {
        Http::fake(['worker.test/*' => Http::sequence()
            ->pushFailedConnection()
            ->push('boom', 502)]);

        foreach (range(1, 2) as $_) {
            try {
                app(LicenseApi::class)->show('k1');
                $this->fail('expected an exception');
            } catch (LicenseApiException $e) {
                $this->assertTrue($e->isUnreachable());
            }
        }
    }

    public function test_calls_fail_cleanly_when_not_configured(): void
    {
        config(['services.licensing.url' => null, 'services.licensing.admin_token' => null]);
        Http::fake();

        $api = app(LicenseApi::class);
        $this->assertFalse($api->isConfigured());

        try {
            $api->issue([]);
            $this->fail('expected an exception');
        } catch (LicenseApiException $e) {
            $this->assertSame('not_configured', $e->errorCode);
        }

        Http::assertNothingSent();
    }
}
