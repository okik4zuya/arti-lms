<?php

namespace Tests\Feature;

use App\Actions\Licensing\SyncLicense;
use App\Models\LicenseIssuance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SyncLicensesCommandTest extends TestCase
{
    use RefreshDatabase;

    private const KEY = 'ARTI-ABCD-EFGH-JKMN-PQRS';

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.licensing.url' => 'https://worker.test/v1', 'services.licensing.admin_token' => 'tok']);
    }

    private function row(string $keyId = 'k1', array $over = []): LicenseIssuance
    {
        $row = new LicenseIssuance(['order_ref' => 'WA-'.$keyId, 'email' => $keyId.'@example.test', 'tier' => 'individual', 'months' => 12]);
        $row->forceFill($over + ['key_id' => $keyId, 'key' => self::KEY, 'status' => LicenseIssuance::ACTIVE, 'seats' => 1])->save();

        return $row;
    }

    private function show(string $status = 'active', array $activations = []): array
    {
        return [
            'license' => ['status' => $status, 'tier' => 'individual', 'seats' => 1, 'expires' => '2099-01-01T00:00:00.000Z', 'updates_until' => '2099-01-01T00:00:00.000Z'],
            'activations' => $activations,
        ];
    }

    public function test_the_worker_wins_on_status_and_activations(): void
    {
        $row = $this->row();
        Http::fake(['worker.test/v1/admin/show*' => Http::response($this->show('revoked', [
            ['machine_hash' => 'a', 'deactivated' => 0],
            ['machine_hash' => 'b', 'deactivated' => 1],
        ]))]);

        app(SyncLicense::class)->handle($row);

        $row->refresh();
        $this->assertSame(LicenseIssuance::REVOKED, $row->status);
        $this->assertSame(1, $row->activations_count);
        $this->assertNotNull($row->last_synced_at);
    }

    public function test_a_license_the_worker_does_not_know_is_marked_missing(): void
    {
        $row = $this->row();
        Http::fake(['worker.test/*' => Http::response(['code' => 'not_found', 'message' => 'unknown key_id'], 404)]);

        app(SyncLicense::class)->handle($row);

        $this->assertSame(LicenseIssuance::MISSING, $row->fresh()->status);
    }

    public function test_a_key_the_worker_rejects_is_marked_stale_and_a_good_one_is_cleared(): void
    {
        $stale = $this->row('k1');
        $good = $this->row('k2', ['key_stale' => true]);

        Http::fake([
            'worker.test/v1/admin/show*' => Http::response($this->show()),
            'worker.test/v1/activations' => Http::sequence()
                ->push(['code' => 'not_found', 'message' => 'no such license key'], 404)
                ->push(['key_id' => 'k2', 'status' => 'active']),
        ]);

        app(SyncLicense::class)->handle($stale, verifyKey: true);
        app(SyncLicense::class)->handle($good, verifyKey: true);

        $this->assertTrue($stale->fresh()->key_stale);
        $this->assertFalse($good->fresh()->key_stale);
        $this->assertNotNull($stale->fresh()->key_checked_at);
    }

    public function test_a_rate_limited_key_check_leaves_the_flag_alone(): void
    {
        $row = $this->row();
        Http::fake([
            'worker.test/v1/admin/show*' => Http::response($this->show()),
            'worker.test/v1/activations' => Http::response(['code' => 'rate_limited', 'message' => 'slow down'], 429),
        ]);

        app(SyncLicense::class)->handle($row, verifyKey: true);

        $this->assertFalse($row->fresh()->key_stale);
        $this->assertNull($row->fresh()->key_checked_at);
    }

    public function test_the_command_syncs_every_row_and_limits_key_checks(): void
    {
        $this->row('k1');
        $this->row('k2');
        $this->row('k3');

        Http::fake([
            'worker.test/v1/admin/show*' => Http::response($this->show()),
            'worker.test/v1/activations' => Http::response(['key_id' => 'x', 'status' => 'active']),
        ]);

        $this->artisan('licenses:sync', ['--verify' => 2])->assertSuccessful();

        Http::assertSent(fn (Request $r) => str_contains($r->url(), '/admin/show'));
        $shows = Http::recorded(fn (Request $r) => str_contains($r->url(), '/admin/show'))->count();
        $checks = Http::recorded(fn (Request $r) => str_ends_with($r->url(), '/activations'))->count();
        $this->assertSame(3, $shows);
        $this->assertSame(2, $checks);
    }

    public function test_the_command_does_nothing_without_configuration(): void
    {
        config(['services.licensing.url' => null]);
        Http::fake();

        $this->artisan('licenses:sync')->assertSuccessful();

        Http::assertNothingSent();
    }
}
