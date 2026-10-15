<?php

namespace Tests\Feature;

use App\Filament\Resources\LicenseIssuanceResource\Pages\ListLicenseIssuances;
use App\Models\Course;
use App\Models\LicenseIssuance;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
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
            'email' => 'new@example.test', 'name' => 'New Buyer', 'tier' => 'individual', 'months' => 12,
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

        $this->get('/jamrud/license-issuances')->assertOk()->assertDontSee(self::KEY)->assertSee('Tersimpan');
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
                'create_learner' => true, 'courses' => [$course->id], 'password' => 'temp-pass-123',
            ]))
            ->assertHasNoFormErrors();

        $learner = User::where('email', 'new@example.test')->firstOrFail();
        $this->assertTrue($learner->hasRole('learner'));
        $this->assertDatabaseHas('course_access', ['user_id' => $learner->id, 'course_id' => $course->id]);
        $this->assertSame($learner->id, LicenseIssuance::where('order_ref', 'WA-NEW')->value('user_id'));
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
