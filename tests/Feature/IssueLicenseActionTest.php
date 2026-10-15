<?php

namespace Tests\Feature;

use App\Actions\Licensing\IssueLicense;
use App\Actions\Licensing\IssueRequest;
use App\Models\LicenseIssuance;
use App\Services\Licensing\LicenseApiException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Tests\TestCase;

class IssueLicenseActionTest extends TestCase
{
    use RefreshDatabase;

    private const KEY = 'ARTI-ABCD-EFGH-JKMN-PQRS';

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.licensing.url' => 'https://worker.test/v1', 'services.licensing.admin_token' => 'tok']);
    }

    private function request(string $order = 'WA-1', string $email = 'buyer@example.test'): IssueRequest
    {
        return new IssueRequest(email: $email, name: 'Buyer', months: 12, orderRef: $order, amountIdr: 99000);
    }

    private function issued(array $over = []): array
    {
        return $over + [
            'key' => self::KEY, 'key_id' => 'k1', 'tier' => 'individual', 'seats' => 1,
            'expires' => '2027-10-15T10:00:00.000Z', 'updates_until' => '2027-10-15T10:00:00.000Z', 'email' => 'buyer@example.test',
        ];
    }

    public function test_issue_stores_the_key_encrypted_and_the_worker_state(): void
    {
        Http::fake(['worker.test/*' => Http::response($this->issued())]);

        $row = app(IssueLicense::class)->handle($this->request());

        $this->assertSame(LicenseIssuance::ACTIVE, $row->status);
        $this->assertSame('k1', $row->key_id);
        $this->assertSame(self::KEY, $row->fresh()->key);
        $this->assertSame('2027-10-15', $row->expires_at->toDateString());

        $raw = DB::table('license_issuances')->where('id', $row->id)->value('key');
        $this->assertNotSame(self::KEY, $raw);
        $this->assertStringNotContainsString('ABCD', $raw);
    }

    public function test_the_key_never_appears_in_serialisation_or_the_event_log(): void
    {
        Http::fake(['worker.test/*' => Http::response($this->issued())]);

        $row = app(IssueLicense::class)->handle($this->request());

        $this->assertArrayNotHasKey('key', $row->toArray());
        $this->assertStringNotContainsString(self::KEY, $row->toJson());
        $this->assertStringNotContainsString(self::KEY, json_encode(DB::table('license_issuance_events')->get()));
    }

    public function test_repeating_an_order_ref_with_a_usable_key_does_not_call_the_worker_again(): void
    {
        Http::fake(['worker.test/*' => Http::response($this->issued())]);

        app(IssueLicense::class)->handle($this->request());
        app(IssueLicense::class)->handle($this->request());

        Http::assertSentCount(1);
        $this->assertSame(1, LicenseIssuance::count());
    }

    public function test_an_unreachable_worker_leaves_a_pending_row_that_can_be_retried(): void
    {
        Http::fake(['worker.test/*' => Http::sequence()->pushFailedConnection()->push($this->issued())]);

        try {
            app(IssueLicense::class)->handle($this->request());
            $this->fail('expected an exception');
        } catch (LicenseApiException $e) {
            $this->assertTrue($e->isUnreachable());
        }

        $pending = LicenseIssuance::firstOrFail();
        $this->assertSame(LicenseIssuance::PENDING, $pending->status);
        $this->assertNotNull($pending->sync_error);

        $row = app(IssueLicense::class)->handle($this->request());

        $this->assertSame($pending->id, $row->id);
        $this->assertSame(LicenseIssuance::ACTIVE, $row->status);
        $this->assertSame(1, LicenseIssuance::count());
    }

    public function test_a_refused_request_removes_the_pending_row(): void
    {
        Http::fake(['worker.test/*' => Http::response(['code' => 'bad_request', 'message' => 'bad email'], 400)]);

        $this->expectException(LicenseApiException::class);

        try {
            app(IssueLicense::class)->handle($this->request());
        } finally {
            $this->assertSame(0, LicenseIssuance::count());
        }
    }

    public function test_order_exists_without_a_local_key_rotates_to_a_fresh_one(): void
    {
        Http::fake([
            'worker.test/v1/admin/issue' => Http::response(['code' => 'order_exists', 'message' => 'already issued', 'key_id' => 'k1'], 409),
            'worker.test/v1/admin/resend' => Http::response(['key_id' => 'k1', 'key' => 'ARTI-NEW1-NEW1-NEW1-NEW1', 'emailed' => 'buyer@example.test']),
            'worker.test/v1/admin/show*' => Http::response([
                'license' => ['key_id' => 'k1', 'status' => 'active', 'tier' => 'individual', 'seats' => 1, 'expires' => '2027-10-15T10:00:00.000Z', 'updates_until' => '2027-10-15T10:00:00.000Z'],
                'activations' => [],
            ]),
        ]);

        // The first response was lost: a pending row exists with no key.
        LicenseIssuance::create([
            'order_ref' => 'WA-1', 'email' => 'buyer@example.test', 'tier' => 'individual', 'months' => 12, 'status' => LicenseIssuance::PENDING,
        ]);

        $row = app(IssueLicense::class)->handle($this->request());

        $this->assertSame('ARTI-NEW1-NEW1-NEW1-NEW1', $row->fresh()->key);
        $this->assertSame('k1', $row->key_id);
        $this->assertSame(LicenseIssuance::ACTIVE, $row->status);
        $this->assertDatabaseHas('license_issuance_events', ['license_issuance_id' => $row->id, 'event' => 'key_rotated']);
    }

    public function test_order_exists_keeps_the_local_key_when_there_is_one(): void
    {
        Http::fake([
            'worker.test/v1/admin/issue' => Http::response(['code' => 'order_exists', 'message' => 'x', 'key_id' => 'k1'], 409),
            'worker.test/v1/admin/show*' => Http::response(['license' => ['status' => 'revoked', 'expires' => '2027-10-15T10:00:00.000Z'], 'activations' => []]),
        ]);

        $existing = (new LicenseIssuance([
            'order_ref' => 'WA-1', 'email' => 'buyer@example.test', 'tier' => 'individual', 'months' => 12,
        ]))->forceFill(['status' => LicenseIssuance::PENDING, 'key' => self::KEY]);
        $existing->save();

        $row = app(IssueLicense::class)->handle($this->request());

        $this->assertSame(self::KEY, $row->fresh()->key);
        $this->assertSame(LicenseIssuance::REVOKED, $row->status);
        Http::assertNotSent(fn ($r) => str_contains($r->url(), '/admin/resend'));
        $this->assertSame($existing->id, $row->id);
    }

    public function test_an_order_ref_cannot_be_reused_for_another_email(): void
    {
        Http::fake(['worker.test/*' => Http::response($this->issued())]);
        app(IssueLicense::class)->handle($this->request());

        $this->expectException(InvalidArgumentException::class);

        app(IssueLicense::class)->handle($this->request(email: 'other@example.test'));
    }
}
