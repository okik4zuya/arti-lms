<?php

namespace Tests\Feature;

use App\Actions\Licensing\ExtendLicense;
use App\Actions\Licensing\ReleaseDevice;
use App\Actions\Licensing\ResetAccountPassword;
use App\Actions\Licensing\RevokeLicense;
use App\Actions\Licensing\RotateKey;
use App\Filament\Resources\LicenseIssuanceResource\Pages\ListLicenseIssuances;
use App\Models\LicenseIssuance;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Support\LicenseMessage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class LicenseActionsTest extends TestCase
{
    use RefreshDatabase;

    private const KEY = 'ARTI-ABCD-EFGH-JKMN-PQRS';

    private const NEW_KEY = 'ARTI-NEWN-KEYK-ABCD-WXYZ';

    private const HASH = '3fa9aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaac21e';

    private User $admin;

    private User $support;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('permissions:sync')->assertSuccessful();
        config(['services.licensing.url' => 'https://worker.test/v1', 'services.licensing.admin_token' => 'tok']);
        Filament::setCurrentPanel('jamrud');

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
        $this->support = User::factory()->create();
        $this->support->assignRole('support');
    }

    private function row(array $over = []): LicenseIssuance
    {
        $row = new LicenseIssuance(['order_ref' => 'WA-'.uniqid(), 'email' => 'buyer@example.test', 'name' => 'Buyer', 'tier' => 'individual', 'months' => 12, 'source' => 'manual']);
        $row->forceFill($over + [
            'key_id' => uniqid('k'), 'key' => self::KEY, 'status' => LicenseIssuance::ACTIVE, 'seats' => 1, 'activations_count' => 1,
            'expires_at' => now()->addMonths(6)->startOfSecond(), 'updates_until' => now()->addMonths(6)->startOfSecond(),
        ])->save();

        return $row;
    }

    private function fakeWorker(array $response = []): void
    {
        Http::fake(['worker.test/*' => Http::response($response)]);
    }

    private function sentTo(string $path): ?array
    {
        $found = null;
        Http::assertSent(function ($request) use ($path, &$found) {
            if (str_ends_with($request->url(), $path)) {
                $found = $request->data();

                return true;
            }

            return false;
        });

        return $found;
    }

    // ---- Extend ---------------------------------------------------------------------------

    public function test_a_paid_extension_adds_months_logs_and_keeps_the_review_queue_clean_with_a_reference(): void
    {
        $row = $this->row();
        $this->fakeWorker(['key_id' => $row->key_id, 'expires' => '2030-01-01T00:00:00.000Z', 'updates_until' => '2030-01-01T00:00:00.000Z']);

        app(ExtendLicense::class)->handle($row, $this->support, 'paid', 12, 'BCA-77');

        $this->assertSame(['key_id' => $row->key_id, 'months' => 12], $this->sentTo('/admin/extend'));
        $fresh = $row->fresh();
        $this->assertSame(2030, $fresh->expires_at->year);
        $this->assertNull($fresh->review_note);
        $this->assertDatabaseHas('license_issuance_events', ['license_issuance_id' => $row->id, 'event' => 'extended', 'user_id' => $this->support->id]);
    }

    public function test_a_paid_extension_without_a_reference_goes_to_review(): void
    {
        $row = $this->row();
        $this->fakeWorker(['key_id' => $row->key_id, 'expires' => '2030-01-01T00:00:00.000Z', 'updates_until' => '2030-01-01T00:00:00.000Z']);

        app(ExtendLicense::class)->handle($row, $this->support, 'paid', 6);

        $this->assertStringContainsString('tanpa referensi', $row->fresh()->review_note);
        $this->assertTrue(LicenseIssuance::needsReview()->whereKey($row->id)->exists());
    }

    public function test_a_compensation_needs_a_reason_is_capped_at_one_month_and_goes_to_review(): void
    {
        $row = $this->row(['payment_verified_at' => now()]);
        $this->fakeWorker(['key_id' => $row->key_id, 'expires' => '2030-01-01T00:00:00.000Z', 'updates_until' => '2030-01-01T00:00:00.000Z']);
        $action = app(ExtendLicense::class);

        try {
            $action->handle($row, $this->support, 'compensation', 2, null, 'Gangguan layanan');
            $this->fail('two months of compensation must be refused');
        } catch (\InvalidArgumentException $e) {
            $this->assertStringContainsString('maksimal 1 bulan', $e->getMessage());
        }

        try {
            $action->handle($row, $this->support, 'compensation', 1);
            $this->fail('a reason is required');
        } catch (\InvalidArgumentException $e) {
            $this->assertStringContainsString('Alasan', $e->getMessage());
        }

        try {
            $action->handle($row, $this->support, 'compensation', 1, null, 'Lainnya');
            $this->fail('"Lainnya" needs a detail');
        } catch (\InvalidArgumentException) {
            $this->addToAssertionCount(1);
        }

        Http::assertNothingSent();

        $action->handle($row, $this->support, 'compensation', 1, null, 'Bug aplikasi', 'crash saat impor');

        $this->assertStringContainsString('Bug aplikasi', $row->fresh()->review_note);
        $this->assertTrue(LicenseIssuance::needsReview()->whereKey($row->id)->exists());
    }

    public function test_extension_refuses_negative_zero_and_too_many_months_without_calling_the_worker(): void
    {
        $row = $this->row();
        $this->fakeWorker();

        foreach ([-1, 0, 13] as $months) {
            try {
                app(ExtendLicense::class)->handle($row, $this->admin, 'paid', $months, 'x');
                $this->fail("{$months} months must be refused");
            } catch (\InvalidArgumentException) {
                $this->addToAssertionCount(1);
            }
        }

        Http::assertNothingSent();
    }

    public function test_a_second_extension_while_the_first_is_running_is_refused(): void
    {
        $row = $this->row();
        $this->fakeWorker();
        $lock = Cache::lock('license-extend:'.$row->getKey(), 30);
        $this->assertTrue($lock->get());

        try {
            app(ExtendLicense::class)->handle($row, $this->admin, 'paid', 12, 'x');
            $this->fail('a locked extension must be refused');
        } catch (\InvalidArgumentException $e) {
            $this->assertStringContainsString('sedang diproses', $e->getMessage());
        }

        Http::assertNothingSent();
        $lock->release();
    }

    public function test_the_extend_action_works_from_the_table_and_a_user_without_permission_is_refused_by_the_class(): void
    {
        $row = $this->row();
        $this->fakeWorker(['key_id' => $row->key_id, 'expires' => '2031-01-01T00:00:00.000Z', 'updates_until' => '2031-01-01T00:00:00.000Z']);

        Livewire::actingAs($this->support)->test(ListLicenseIssuances::class)
            ->callAction(TestAction::make('extend')->table($row), ['kind' => 'paid', 'months' => 12, 'payment_ref' => 'TRX'])
            ->assertHasNoFormErrors();

        $this->assertSame(2031, $row->fresh()->expires_at->year);

        $nobody = User::factory()->create();
        $this->expectException(AuthorizationException::class);
        app(ExtendLicense::class)->handle($row, $nobody, 'paid', 1, 'x');
    }

    public function test_an_unreachable_worker_during_extend_leaves_the_row_untouched(): void
    {
        $row = $this->row();
        $before = $row->expires_at;
        Http::fake(['worker.test/*' => Http::failedConnection()]);

        Livewire::actingAs($this->support)->test(ListLicenseIssuances::class)
            ->callAction(TestAction::make('extend')->table($row), ['kind' => 'paid', 'months' => 12, 'payment_ref' => 'TRX'])
            ->assertNotified('Worker menolak atau tidak terjangkau');

        $this->assertTrue($row->fresh()->expires_at->equalTo($before));
        $this->assertDatabaseMissing('license_issuance_events', ['event' => 'extended']);
    }

    // ---- Release device -------------------------------------------------------------------

    public function test_support_releases_a_device_with_a_reason_and_it_is_logged(): void
    {
        $row = $this->row();
        $this->fakeWorker(['ok' => true]);

        app(ReleaseDevice::class)->handle($row, $this->support, self::HASH, 'Ganti komputer');

        $this->assertSame(['key_id' => $row->key_id, 'machine_hash' => self::HASH], $this->sentTo('/admin/deactivate'));
        $this->assertSame(0, $row->fresh()->activations_count);
        $this->assertDatabaseHas('license_issuance_events', ['license_issuance_id' => $row->id, 'event' => 'device_released', 'user_id' => $this->support->id]);
    }

    public function test_releasing_needs_a_valid_reason_and_a_found_machine(): void
    {
        $row = $this->row();
        $this->fakeWorker(['ok' => false]);

        try {
            app(ReleaseDevice::class)->handle($row, $this->support, self::HASH, null);
            $this->fail('reason required');
        } catch (\InvalidArgumentException) {
            $this->addToAssertionCount(1);
        }

        Http::assertNothingSent();

        try {
            app(ReleaseDevice::class)->handle($row, $this->support, self::HASH, 'Instal ulang');
            $this->fail('unknown machine must be reported');
        } catch (\InvalidArgumentException $e) {
            $this->assertStringContainsString('tidak ditemukan', $e->getMessage());
        }

        $this->assertDatabaseMissing('license_issuance_events', ['event' => 'device_released']);
    }

    public function test_support_is_capped_at_three_releases_a_year_but_an_admin_is_not(): void
    {
        $row = $this->row();
        $this->fakeWorker(['ok' => true]);

        for ($i = 0; $i < 3; $i++) {
            app(ReleaseDevice::class)->handle($row, $this->support, self::HASH, 'Ganti komputer');
        }

        try {
            app(ReleaseDevice::class)->handle($row, $this->support, self::HASH, 'Ganti komputer');
            $this->fail('the fourth release by support must be refused');
        } catch (\InvalidArgumentException $e) {
            $this->assertStringContainsString('Minta admin', $e->getMessage());
        }

        app(ReleaseDevice::class)->handle($row, $this->admin, self::HASH, 'Ganti komputer');

        $this->assertSame(4, $row->events()->where('event', 'device_released')->count());
    }

    public function test_releases_older_than_a_year_do_not_count_against_the_cap(): void
    {
        $row = $this->row();
        $this->fakeWorker(['ok' => true]);

        for ($i = 0; $i < 3; $i++) {
            $row->logEvent('device_released', $this->support->id);
        }
        $row->events()->update(['created_at' => now()->subDays(400)]);

        app(ReleaseDevice::class)->handle($row, $this->support, self::HASH, 'Ganti komputer');

        $this->assertSame(1, app(ReleaseDevice::class)->releasedThisYear($row));
    }

    // ---- Rotate key -----------------------------------------------------------------------

    public function test_rotating_stores_the_new_key_clears_the_sent_flag_and_logs_the_reason_not_the_key(): void
    {
        $row = $this->row(['wa_sent_at' => now(), 'key_stale' => true]);
        $this->fakeWorker(['key_id' => $row->key_id, 'key' => self::NEW_KEY, 'emailed' => 'buyer@example.test']);

        app(RotateKey::class)->handle($row, $this->support, 'Key bocor');

        $fresh = $row->fresh();
        $this->assertSame(self::NEW_KEY, $fresh->key);
        $this->assertFalse($fresh->key_stale);
        $this->assertNull($fresh->wa_sent_at);

        $event = $row->events()->where('event', 'key_rotated')->firstOrFail();
        $this->assertSame('Key bocor', $event->detail);
        $this->assertStringNotContainsString(self::NEW_KEY, json_encode($event->toArray()));
    }

    public function test_rotating_requires_a_reason_and_keeps_the_old_key_when_the_worker_returns_none(): void
    {
        $row = $this->row();
        $this->fakeWorker(['key_id' => $row->key_id, 'emailed' => 'x']);

        try {
            app(RotateKey::class)->handle($row, $this->support, null);
            $this->fail('reason required');
        } catch (\InvalidArgumentException) {
            $this->addToAssertionCount(1);
        }

        try {
            app(RotateKey::class)->handle($row, $this->support, 'Key hilang');
            $this->fail('a response without a key must not wipe the stored one');
        } catch (\InvalidArgumentException) {
            $this->addToAssertionCount(1);
        }

        $this->assertSame(self::KEY, $row->fresh()->key);
    }

    // ---- Revoke ---------------------------------------------------------------------------

    public function test_only_an_admin_revokes_and_must_type_the_email(): void
    {
        $row = $this->row();
        $this->fakeWorker(['key_id' => $row->key_id, 'status' => 'revoked']);

        try {
            app(RevokeLicense::class)->handle($row, $this->support, 'Refund', null, 'buyer@example.test');
            $this->fail('support must not revoke');
        } catch (AuthorizationException) {
            $this->addToAssertionCount(1);
        }

        try {
            app(RevokeLicense::class)->handle($row, $this->admin, 'Refund', null, 'salah@example.test');
            $this->fail('a wrong confirmation email must be refused');
        } catch (\InvalidArgumentException) {
            $this->addToAssertionCount(1);
        }

        Http::assertNothingSent();

        app(RevokeLicense::class)->handle($row, $this->admin, 'Refund', null, 'BUYER@example.test');

        $this->assertSame(['key_id' => $row->key_id, 'reason' => 'Refund'], $this->sentTo('/admin/revoke'));
        $this->assertSame(LicenseIssuance::REVOKED, $row->fresh()->status);
        $this->assertDatabaseHas('license_issuance_events', ['license_issuance_id' => $row->id, 'event' => 'revoked', 'user_id' => $this->admin->id]);
    }

    public function test_undoing_a_revocation_restores_the_status_and_cannot_be_used_on_an_active_license(): void
    {
        $revoked = $this->row(['status' => LicenseIssuance::REVOKED]);
        $active = $this->row();
        $this->fakeWorker(['key_id' => $revoked->key_id, 'status' => 'active']);

        app(RevokeLicense::class)->handle($revoked, $this->admin, 'Salah cabut', undo: true);

        $this->assertSame(LicenseIssuance::ACTIVE, $revoked->fresh()->status);
        $this->assertTrue($this->sentTo('/admin/revoke')['undo']);

        $this->expectException(\InvalidArgumentException::class);
        app(RevokeLicense::class)->handle($active, $this->admin, 'Salah cabut', undo: true);
    }

    public function test_revoke_and_delete_buttons_are_hidden_from_support_and_shown_to_admin(): void
    {
        $row = $this->row();
        $pending = $this->row(['status' => LicenseIssuance::PENDING, 'key_id' => null, 'key' => null]);

        Livewire::actingAs($this->support)->test(ListLicenseIssuances::class)
            ->assertActionHidden(TestAction::make('revoke')->table($row))
            ->assertActionHidden(TestAction::make('deletePending')->table($pending))
            ->assertActionHidden(TestAction::make('markReviewed')->table($row));

        Livewire::actingAs($this->admin)->test(ListLicenseIssuances::class)
            ->assertActionVisible(TestAction::make('revoke')->table($row))
            ->assertActionVisible(TestAction::make('deletePending')->table($pending));
    }

    public function test_the_revoke_action_through_the_table_requires_the_typed_email(): void
    {
        $row = $this->row();
        $this->fakeWorker(['key_id' => $row->key_id, 'status' => 'revoked']);

        Livewire::actingAs($this->admin)->test(ListLicenseIssuances::class)
            ->callAction(TestAction::make('revoke')->table($row), ['reason' => 'Refund', 'confirm_email' => 'nope@example.test'])
            ->assertNotified('Tidak bisa diproses');

        $this->assertSame(LicenseIssuance::ACTIVE, $row->fresh()->status);

        Livewire::actingAs($this->admin)->test(ListLicenseIssuances::class)
            ->callAction(TestAction::make('revoke')->table($row), ['reason' => 'Refund', 'confirm_email' => 'buyer@example.test'])
            ->assertHasNoFormErrors();

        $this->assertSame(LicenseIssuance::REVOKED, $row->fresh()->status);
    }

    // ---- Reset account password -----------------------------------------------------------

    public function test_support_gets_a_one_time_link_that_is_returned_once_and_not_stored(): void
    {
        $learner = User::factory()->create(['email' => 'buyer@example.test', 'password' => 'lama-sandi-1']);
        $learner->assignRole('learner');
        $oldHash = $learner->password;
        $row = $this->row(['user_id' => $learner->id]);

        $url = app(ResetAccountPassword::class)->handle($row, $this->support);

        $this->assertStringContainsString('b=welcome', $url);
        // The current password is untouched until the buyer uses the link; no password is generated anywhere.
        $this->assertSame($oldHash, $learner->fresh()->password);

        $event = $row->events()->where('event', 'account_password_link')->firstOrFail();
        $this->assertSame($this->support->id, $event->user_id);
        $token = basename(parse_url($url, PHP_URL_PATH));
        $this->assertStringNotContainsString($token, json_encode($event->toArray()));
        $this->assertStringNotContainsString($token, json_encode(\DB::table('license_issuances')->get()));
        $this->assertStringNotContainsString($token, json_encode(\DB::table('license_issuance_events')->get()));

        $this->get($url)->assertOk()->assertSee('Atur password akun belajar');
    }

    public function test_an_admin_or_support_account_can_never_be_reset_from_here(): void
    {
        foreach ([$this->admin, $this->support] as $staff) {
            $row = $this->row(['user_id' => $staff->id]);
            $hash = $staff->password;

            try {
                app(ResetAccountPassword::class)->handle($row, $this->admin);
                $this->fail('a staff account must not be reset');
            } catch (\InvalidArgumentException $e) {
                $this->assertStringContainsString('admin atau support', $e->getMessage());
            }

            $this->assertSame($hash, $staff->fresh()->password);
        }
    }

    public function test_a_license_without_an_account_and_a_user_without_permission_are_refused(): void
    {
        try {
            app(ResetAccountPassword::class)->handle($this->row(), $this->support);
            $this->fail('no account to reset');
        } catch (\InvalidArgumentException) {
            $this->addToAssertionCount(1);
        }

        $learner = User::factory()->create();
        $learner->assignRole('learner');

        $this->expectException(AuthorizationException::class);
        app(ResetAccountPassword::class)->handle($this->row(['user_id' => $learner->id]), User::factory()->create());
    }

    public function test_the_link_action_leaves_the_password_alone_and_points_to_the_key_message(): void
    {
        $learner = User::factory()->create(['email' => 'buyer@example.test']);
        $learner->assignRole('learner');
        $row = $this->row(['user_id' => $learner->id]);

        $oldHash = $learner->password;

        Livewire::actingAs($this->support)->test(ListLicenseIssuances::class)
            ->callAction(TestAction::make('resetAccountPassword')->table($row))
            ->assertNotified('Tautan dibuat. Kirim pesan yang berisi tautan itu.');

        $this->assertSame($oldHash, $learner->fresh()->password);
        $this->assertDatabaseHas('license_issuance_events', ['license_issuance_id' => $row->id, 'event' => 'account_password_link', 'user_id' => $this->support->id]);
    }

    public function test_after_requesting_a_link_the_key_modal_replaces_the_confirmation_and_carries_it(): void
    {
        $learner = User::factory()->create(['email' => 'buyer@example.test']);
        $learner->assignRole('learner');
        $row = $this->row(['user_id' => $learner->id]);

        $component = Livewire::actingAs($this->support)->test(ListLicenseIssuances::class)
            ->mountAction(TestAction::make('resetAccountPassword')->table($row));

        $component->callMountedAction();

        $mounted = $component->instance()->mountedActions;
        $this->assertCount(1, $mounted);
        $this->assertSame('sendKey', $mounted[0]['name']);
        $url = $mounted[0]['arguments']['set_password_url'] ?? null;
        $this->assertStringContainsString('b=welcome', (string) $url);
        $this->assertStringContainsString((string) $url, $mounted[0]['data']['message'] ?? '');
        $this->assertStringNotContainsString('Password sementara', $mounted[0]['data']['message'] ?? '');
    }

    public function test_after_rotating_the_key_the_key_modal_opens_with_the_new_key(): void
    {
        $row = $this->row();
        $this->fakeWorker(['key_id' => $row->key_id, 'key' => self::NEW_KEY, 'emailed' => 'x']);

        $component = Livewire::actingAs($this->support)->test(ListLicenseIssuances::class)
            ->mountAction(TestAction::make('rotateKey')->table($row), ['reason' => 'Key bocor']);
        $component->setActionData(['reason' => 'Key bocor'])->callMountedAction();

        $mounted = $component->instance()->mountedActions;
        $this->assertCount(1, $mounted);
        $this->assertSame('sendKey', $mounted[0]['name']);
        $this->assertSame(self::NEW_KEY, $mounted[0]['data']['key'] ?? null);
    }

    public function test_the_reset_button_is_hidden_without_an_account_or_permission(): void
    {
        $learner = User::factory()->create();
        $learner->assignRole('learner');
        $withAccount = $this->row(['user_id' => $learner->id]);
        $without = $this->row();
        $viewer = User::factory()->create();
        $viewer->givePermissionTo('license.view');

        Livewire::actingAs($this->support)->test(ListLicenseIssuances::class)
            ->assertActionVisible(TestAction::make('resetAccountPassword')->table($withAccount))
            ->assertActionHidden(TestAction::make('resetAccountPassword')->table($without));

        Livewire::actingAs($viewer)->test(ListLicenseIssuances::class)
            ->assertActionHidden(TestAction::make('resetAccountPassword')->table($withAccount));
    }

    // ---- Delete pending, review -----------------------------------------------------------

    public function test_an_admin_can_delete_a_pending_row_but_never_an_issued_license(): void
    {
        $pending = $this->row(['status' => LicenseIssuance::PENDING, 'key_id' => null, 'key' => null]);
        $issued = $this->row();

        Livewire::actingAs($this->admin)->test(ListLicenseIssuances::class)
            ->callAction(TestAction::make('deletePending')->table($pending))
            ->assertHasNoFormErrors();

        $this->assertModelMissing($pending);

        Livewire::actingAs($this->admin)->test(ListLicenseIssuances::class)
            ->assertActionHidden(TestAction::make('deletePending')->table($issued));

        $this->assertModelExists($issued);
    }

    public function test_an_admin_clears_a_review_note_and_it_leaves_the_queue(): void
    {
        $row = $this->row(['payment_verified_at' => now(), 'review_note' => 'Lisensi gratis (harga 0)']);

        $this->assertTrue(LicenseIssuance::needsReview()->whereKey($row->id)->exists());

        Livewire::actingAs($this->admin)->test(ListLicenseIssuances::class)
            ->callAction(TestAction::make('markReviewed')->table($row));

        $this->assertNull($row->fresh()->review_note);
        $this->assertFalse(LicenseIssuance::needsReview()->whereKey($row->id)->exists());
        $this->assertDatabaseHas('license_issuance_events', ['license_issuance_id' => $row->id, 'event' => 'reviewed', 'user_id' => $this->admin->id]);
    }

    public function test_a_free_license_lands_in_the_review_queue_after_issue(): void
    {
        $this->fakeWorker([
            'key' => self::KEY, 'key_id' => 'knew', 'tier' => 'individual', 'seats' => 1,
            'expires' => '2099-01-01T00:00:00.000Z', 'updates_until' => '2099-01-01T00:00:00.000Z', 'email' => 'gift@example.test',
        ]);

        Livewire::actingAs($this->support)->test(ListLicenseIssuances::class)
            ->callAction('issue', ['email' => 'gift@example.test', 'name' => 'Gift', 'months' => 12, 'seats' => 1, 'amount_idr' => 0, 'order_ref' => 'WA-GIFT', 'notes' => 'Hadiah reviewer']);

        $row = LicenseIssuance::where('order_ref', 'WA-GIFT')->firstOrFail();
        $this->assertNotNull($row->review_note);
        $this->assertTrue(LicenseIssuance::needsReview()->whereKey($row->id)->exists());
    }
}
