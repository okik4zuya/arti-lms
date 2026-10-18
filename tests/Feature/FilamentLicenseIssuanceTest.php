<?php

namespace Tests\Feature;

use App\Filament\Resources\LicenseIssuanceResource\Pages\ListLicenseIssuances;
use App\Models\Course;
use App\Models\LicenseIssuance;
use App\Models\User;
use App\Support\LicenseMessage;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Support\LicenseHistory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FilamentLicenseIssuanceTest extends TestCase
{
    use RefreshDatabase;

    private const KEY = 'ARTI-ABCD-EFGH-JKMN-PQRS';

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.licensing.url' => 'https://worker.test/v1', 'services.licensing.admin_token' => 'tok']);

        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'learner']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->actingAs($this->admin);
        Filament::setCurrentPanel('jamrud');
    }

    private function row(array $over = []): LicenseIssuance
    {
        $row = new LicenseIssuance(['order_ref' => 'WA-1', 'email' => 'buyer@example.test', 'name' => 'Buyer', 'tier' => 'individual', 'months' => 12, 'buyer_whatsapp' => '081234567890']);
        $row->forceFill($over + [
            'key_id' => 'k1', 'key' => self::KEY, 'status' => LicenseIssuance::ACTIVE, 'seats' => 1,
            'expires_at' => now()->addYear(), 'last_synced_at' => now(),
        ])->save();

        return $row;
    }

    private function issueData(array $over = []): array
    {
        return $over + [
            'email' => 'new@example.test', 'name' => 'New Buyer', 'months' => 12, 'seats' => 1,
            'amount_idr' => 99000, 'order_ref' => 'WA-NEW', 'buyer_whatsapp' => '081200000000',
        ];
    }

    private function workerIssues(): void
    {
        Http::fake(['worker.test/*' => Http::response([
            'key' => self::KEY, 'key_id' => 'knew', 'tier' => 'individual', 'seats' => 1,
            'expires' => '2099-01-01T00:00:00.000Z', 'updates_until' => '2099-01-01T00:00:00.000Z', 'email' => 'new@example.test',
        ])]);
    }

    public function test_the_page_renders_for_an_admin_and_is_forbidden_for_a_learner(): void
    {
        $this->row();

        $this->get('/jamrud/license-issuances')->assertOk()->assertSee('buyer@example.test');

        $learner = User::factory()->create();
        $learner->assignRole('learner');
        $this->actingAs($learner)->get('/jamrud/license-issuances')->assertForbidden();
    }

    public function test_the_page_never_prints_the_key_in_the_table(): void
    {
        $this->row();

        $this->get('/jamrud/license-issuances')->assertOk()->assertDontSee(self::KEY);
    }

    public function test_admin_can_issue_a_license(): void
    {
        $this->workerIssues();

        Livewire::test(ListLicenseIssuances::class)
            ->callAction('issue', $this->issueData())
            ->assertHasNoFormErrors();

        $row = LicenseIssuance::where('order_ref', 'WA-NEW')->firstOrFail();
        $this->assertSame('knew', $row->key_id);
        $this->assertSame(self::KEY, $row->key);
        $this->assertSame($this->admin->id, $row->issued_by);
        $this->assertSame(99000, $row->amount_idr);
    }

    public function test_issuing_can_create_a_learner_and_grant_courses(): void
    {
        $this->workerIssues();
        $course = Course::create(['slug' => 'example-course', 'title' => 'Example Course']);

        Livewire::test(ListLicenseIssuances::class)
            ->callAction('issue', $this->issueData([
                'create_learner' => true, 'courses' => [$course->id],
            ]))
            ->assertHasNoFormErrors()
            ->assertNotified('Akun learner dibuat');

        $learner = User::where('email', 'new@example.test')->firstOrFail();
        $this->assertTrue($learner->hasRole('learner'));
        $this->assertDatabaseHas('course_access', ['user_id' => $learner->id, 'course_id' => $course->id]);
        $this->assertSame($learner->id, LicenseIssuance::where('order_ref', 'WA-NEW')->value('user_id'));

        $this->assertNotEmpty($learner->password);
    }

    public function test_the_key_modal_message_carries_the_set_password_link_only_when_it_is_passed_in(): void
    {
        $learner = User::factory()->create(['email' => 'new@example.test']);
        $row = $this->row(['user_id' => $learner->id]);
        $link = 'https://arti.test/atur-sandi/tok123?email=new%40example.test&b=welcome&signature=abc';

        $with = Livewire::test(ListLicenseIssuances::class)
            ->mountAction(TestAction::make('sendKey')->table($row)->arguments(['set_password_url' => $link]));

        $message = $with->instance()->mountedActions[0]['data']['message'];
        $this->assertStringContainsString($link, $message);
        $this->assertStringContainsString('lewat tautan sekali pakai ini (tanpa batas waktu', $message);
        $this->assertStringNotContainsString('Password sementara', $message);

        $without = Livewire::test(ListLicenseIssuances::class)->mountAction(TestAction::make('sendKey')->table($row));

        $this->assertStringNotContainsString('atur-sandi', $without->instance()->mountedActions[0]['data']['message']);
        $this->assertStringNotContainsString('Password sementara', $without->instance()->mountedActions[0]['data']['message']);
    }

    public function test_the_account_password_is_not_stored_anywhere(): void
    {
        $this->assertFalse(Schema::hasColumn('license_issuances', 'account_password'));

        $this->workerIssues();
        Livewire::test(ListLicenseIssuances::class)->callAction('issue', $this->issueData(['create_learner' => true]));

        $row = LicenseIssuance::where('order_ref', 'WA-NEW')->firstOrFail();

        foreach (['license_issuances', 'license_issuance_events'] as $table) {
            $this->assertStringNotContainsString('password', strtolower(json_encode(\DB::table($table)->get())), $table);
        }

        $this->assertStringNotContainsString('Password sementara', LicenseMessage::for($row));
        $this->assertStringNotContainsString('atur-sandi', LicenseMessage::for($row));
    }

    public function test_issuing_with_a_new_account_makes_a_one_time_link_and_stores_no_password(): void
    {
        $this->workerIssues();

        Livewire::test(ListLicenseIssuances::class)
            ->callAction('issue', $this->issueData(['create_learner' => true]))
            ->assertHasNoFormErrors();

        $learner = User::where('email', 'new@example.test')->firstOrFail();
        // The link itself is shown only in the key modal; what is stored is the hashed token the link opens.
        $this->assertDatabaseHas('password_reset_tokens', ['email' => 'new@example.test']);
        $this->assertTrue(\Hash::check('password', $learner->password) === false);
        $this->assertStringNotContainsString('atur-sandi', json_encode(\DB::table('license_issuance_events')->get()));
    }

    public function test_the_message_carries_the_login_and_the_link_when_one_was_just_made(): void
    {
        $learner = User::factory()->create(['email' => 'new@example.test']);
        $row = $this->row(['user_id' => $learner->id]);

        $message = LicenseMessage::for($row, 'https://arti.test/atur-sandi/temp-token');

        $this->assertStringContainsString('Email: new@example.test', $message);
        $this->assertStringContainsString('https://arti.test/atur-sandi/temp-token', $message);
        $this->assertStringNotContainsString('Password sementara', $message);
        $this->assertStringContainsString(route('login'), $message);
        $this->assertStringNotContainsString('Segera ganti password', $message);
        $this->assertStringNotContainsString('{', $message);

        // Opened later without the argument, the same row carries no link.
        $later = LicenseMessage::for($row->fresh());
        $this->assertStringNotContainsString('temp-token', $later);
        $this->assertStringNotContainsString('Password sementara', $later);
    }

    public function test_the_message_gives_only_the_email_for_an_existing_account_and_nothing_without_one(): void
    {
        $learner = User::factory()->create(['email' => 'old@example.test']);

        $existing = LicenseMessage::for($this->row(['user_id' => $learner->id]));
        $this->assertStringContainsString('old@example.test', $existing);
        $this->assertStringNotContainsString('Password sementara', $existing);

        $none = LicenseMessage::for($this->row(['order_ref' => 'WA-2', 'key_id' => 'k2']));
        $this->assertStringNotContainsString('Akun belajar', $none);
        $this->assertStringNotContainsString('{', $none);
    }

    public function test_the_course_picker_appears_as_soon_as_the_learner_toggle_is_on(): void
    {
        $component = Livewire::test(ListLicenseIssuances::class)->mountAction('issue');

        $component->assertSchemaComponentHidden('courses', 'mountedActionSchema0');

        $component->fillForm(['create_learner' => true], 'mountedActionSchema0')
            ->assertSchemaComponentVisible('courses', 'mountedActionSchema0');
    }

    public function test_the_issue_call_to_the_worker_carries_no_tier(): void
    {
        $this->workerIssues();

        Livewire::test(ListLicenseIssuances::class)->callAction('issue', $this->issueData());

        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/admin/issue')
            && ! array_key_exists('tier', $request->data())
            && $request->data()['seats'] === 1);
    }

    public function test_the_message_has_no_tier_placeholder_or_word(): void
    {
        $message = LicenseMessage::for($this->row());

        $this->assertStringNotContainsString('individual', $message);
        $this->assertStringNotContainsString('{', $message);
        $this->assertStringContainsString('(1 perangkat, berlaku sampai', $message);
    }

    public function test_edit_contact_changes_the_contact_fields_but_not_the_email_and_logs_it(): void
    {
        $row = $this->row();

        Livewire::test(ListLicenseIssuances::class)
            ->callAction(TestAction::make('editContact')->table($row), ['name' => 'Renamed', 'buyer_whatsapp' => '0899', 'notes' => 'n', 'email' => 'x@y.test'])
            ->assertHasNoFormErrors();

        $fresh = $row->fresh();
        $this->assertSame('Renamed', $fresh->name);
        $this->assertSame('0899', $fresh->buyer_whatsapp);
        $this->assertSame('buyer@example.test', $fresh->email);
        $this->assertDatabaseHas('license_issuance_events', ['license_issuance_id' => $row->id, 'event' => 'contact_edited', 'user_id' => $this->admin->id]);
    }

    public function test_history_merges_worker_and_local_events_and_shows_who_issued(): void
    {
        $row = $this->row(['issued_by' => $this->admin->id]);
        $row->logEvent('key_revealed', $this->admin->id);
        Http::fake(['worker.test/*' => Http::response([
            'license' => ['status' => 'active'],
            'activations' => [['machine_hash' => '3fa9aaaaaaaaaaaaaaaac21e', 'first_seen' => '2026-10-02T10:00:00Z', 'last_seen' => '2026-10-14T10:00:00Z', 'deactivated' => 0]],
            'events' => [['at' => '2026-10-02T10:00:00Z', 'event' => 'activated', 'detail' => 'ok']],
        ])]);

        $html = view('filament.license-history', LicenseHistory::for($row))->render();

        $this->assertStringContainsString('3fa9...c21e', $html);
        $this->assertStringContainsString('activated', $html);
        $this->assertStringContainsString('key_revealed', $html);
        $this->assertStringContainsString($this->admin->name, $html);
        $this->assertStringNotContainsString(self::KEY, $html);
    }

    public function test_history_still_renders_when_the_worker_is_unreachable(): void
    {
        $row = $this->row();
        $row->logEvent('key_revealed', $this->admin->id);
        Http::fake(['worker.test/*' => Http::response(null, 500)]);

        $html = view('filament.license-history', LicenseHistory::for($row))->render();

        $this->assertStringContainsString('Data Worker tidak bisa diambil', $html);
        $this->assertStringContainsString('key_revealed', $html);
    }

    public function test_quick_filters_narrow_the_table(): void
    {
        $soon = $this->row(['order_ref' => 'A', 'key_id' => 'ka', 'email' => 'soon@example.test', 'expires_at' => now()->addDays(10)]);
        $later = $this->row(['order_ref' => 'B', 'key_id' => 'kb', 'email' => 'later@example.test']);
        $stale = $this->row(['order_ref' => 'C', 'key_id' => 'kc', 'email' => 'stale@example.test', 'key_stale' => true]);
        $failed = $this->row(['order_ref' => 'D', 'key_id' => 'kd', 'email' => 'failed@example.test', 'sync_error' => 'boom']);
        $sent = $this->row(['order_ref' => 'E', 'key_id' => 'ke', 'email' => 'sent@example.test', 'wa_sent_at' => now()]);

        Livewire::test(ListLicenseIssuances::class)
            ->filterTable('expiring')->assertCanSeeTableRecords([$soon])->assertCanNotSeeTableRecords([$later, $stale, $failed])
            ->removeTableFilters()
            ->filterTable('stale_key')->assertCanSeeTableRecords([$stale])->assertCanNotSeeTableRecords([$soon, $later])
            ->removeTableFilters()
            ->filterTable('sync_failed')->assertCanSeeTableRecords([$failed])->assertCanNotSeeTableRecords([$soon, $later])
            ->removeTableFilters()
            ->filterTable('wa_unsent')->assertCanSeeTableRecords([$later])->assertCanNotSeeTableRecords([$sent]);
    }

    public function test_the_table_shows_who_issued_the_license(): void
    {
        $this->row(['issued_by' => $this->admin->id]);

        Livewire::test(ListLicenseIssuances::class)->assertSee($this->admin->name);
    }

    public function test_an_unreachable_worker_keeps_a_pending_row_and_can_be_retried_from_the_table(): void
    {
        Http::fake(['worker.test/*' => Http::sequence()->pushFailedConnection()->push([
            'key' => self::KEY, 'key_id' => 'knew', 'tier' => 'individual', 'seats' => 1,
            'expires' => '2099-01-01T00:00:00.000Z', 'updates_until' => '2099-01-01T00:00:00.000Z', 'email' => 'new@example.test',
        ])]);

        Livewire::test(ListLicenseIssuances::class)->callAction('issue', $this->issueData());

        $pending = LicenseIssuance::where('order_ref', 'WA-NEW')->firstOrFail();
        $this->assertSame(LicenseIssuance::PENDING, $pending->status);

        // The failed issue keeps its modal open (halt), so retry from a fresh page load.
        Livewire::test(ListLicenseIssuances::class)->callAction(TestAction::make('sync')->table($pending));

        $this->assertSame(LicenseIssuance::ACTIVE, $pending->fresh()->status);
    }

    public function test_the_issue_button_is_disabled_until_the_worker_is_configured(): void
    {
        config(['services.licensing.url' => null]);

        Livewire::test(ListLicenseIssuances::class)->assertActionDisabled('issue');
    }

    public function test_send_key_logs_who_opened_it_and_marks_the_message_as_sent(): void
    {
        $row = $this->row();

        Livewire::test(ListLicenseIssuances::class)
            ->callAction(TestAction::make('sendKey')->table($row))
            ->assertHasNoFormErrors();

        $this->assertNotNull($row->fresh()->wa_sent_at);
        $this->assertDatabaseHas('license_issuance_events', ['license_issuance_id' => $row->id, 'event' => 'key_revealed', 'user_id' => $this->admin->id]);
        $this->assertDatabaseHas('license_issuance_events', ['license_issuance_id' => $row->id, 'event' => 'message_sent']);
    }

    public function test_send_key_is_hidden_for_a_stale_key(): void
    {
        $row = $this->row(['key_stale' => true]);

        Livewire::test(ListLicenseIssuances::class)
            ->assertActionHidden(TestAction::make('sendKey')->table($row));
    }

    public function test_sync_action_refreshes_a_row_from_the_worker(): void
    {
        $row = $this->row();
        Http::fake(['worker.test/*' => Http::response([
            'license' => ['status' => 'revoked', 'expires' => '2099-01-01T00:00:00.000Z'], 'activations' => [],
        ])]);

        Livewire::test(ListLicenseIssuances::class)->callAction(TestAction::make('sync')->table($row));

        $this->assertSame(LicenseIssuance::REVOKED, $row->fresh()->status);
    }
}
