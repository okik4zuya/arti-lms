<?php

namespace Tests\Feature;

use App\Actions\Licensing\IssueLicense;
use App\Actions\Licensing\IssueRequest;
use App\Filament\Resources\LicenseIssuanceResource;
use App\Filament\Resources\LicenseIssuanceResource\Pages\ListLicenseIssuances;
use App\Models\LicenseIssuance;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Tests\TestCase;

class LicensePaymentProofTest extends TestCase
{
    use RefreshDatabase;

    private const KEY = 'ARTI-ABCD-EFGH-JKMN-PQRS';

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('permissions:sync')->assertSuccessful();
        config(['services.licensing.url' => 'https://worker.test/v1', 'services.licensing.admin_token' => 'tok']);
        Filament::setCurrentPanel('jamrud');
        Storage::fake('local');
    }

    private function userWith(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    private function row(array $over = []): LicenseIssuance
    {
        $row = new LicenseIssuance(['order_ref' => 'WA-'.uniqid(), 'email' => uniqid().'@example.test', 'name' => 'Buyer', 'tier' => 'individual', 'months' => 12, 'source' => 'manual']);
        $row->forceFill($over + ['key_id' => uniqid('k'), 'key' => self::KEY, 'status' => LicenseIssuance::ACTIVE, 'seats' => 1, 'expires_at' => now()->addYear()])->save();

        return $row;
    }

    private function workerIssues(): void
    {
        Http::fake(['worker.test/*' => Http::response([
            'key' => self::KEY, 'key_id' => 'knew', 'tier' => 'individual', 'seats' => 1,
            'expires' => '2099-01-01T00:00:00.000Z', 'updates_until' => '2099-01-01T00:00:00.000Z', 'email' => 'new@example.test',
        ])]);
    }

    private function issueData(array $over = []): array
    {
        return $over + ['email' => 'new@example.test', 'name' => 'New', 'months' => 12, 'seats' => 1, 'amount_idr' => 99000, 'order_ref' => 'WA-NEW'];
    }

    public function test_payment_state_is_derived_from_reference_proof_and_verification(): void
    {
        $this->assertSame('missing', $this->row()->paymentState());
        $this->assertSame('waiting', $this->row(['payment_ref' => 'TRX1'])->paymentState());
        $this->assertSame('waiting', $this->row(['payment_proof_path' => 'license-proofs/a.png'])->paymentState());
        $this->assertSame('verified', $this->row(['payment_verified_at' => now()])->paymentState());
        $this->assertSame('none', $this->row(['status' => LicenseIssuance::PENDING])->paymentState());
    }

    public function test_issuing_stores_the_reference_and_the_license_enters_the_review_queue(): void
    {
        $this->workerIssues();

        Livewire::actingAs($this->userWith('support'))->test(ListLicenseIssuances::class)
            ->callAction('issue', $this->issueData(['payment_ref' => 'BCA-123']))
            ->assertHasNoFormErrors();

        $row = LicenseIssuance::where('order_ref', 'WA-NEW')->firstOrFail();
        $this->assertSame('BCA-123', $row->payment_ref);
        $this->assertNull($row->payment_verified_at);
        $this->assertTrue(LicenseIssuance::needsReview()->whereKey($row->id)->exists());
    }

    public function test_a_license_can_be_issued_without_proof_and_is_flagged_as_missing(): void
    {
        $this->workerIssues();

        Livewire::actingAs($this->userWith('support'))->test(ListLicenseIssuances::class)
            ->callAction('issue', $this->issueData());

        $this->assertSame('missing', LicenseIssuance::where('order_ref', 'WA-NEW')->firstOrFail()->paymentState());
    }

    public function test_a_free_license_needs_a_reason_in_the_notes(): void
    {
        $this->workerIssues();

        Livewire::actingAs($this->userWith('support'))->test(ListLicenseIssuances::class)
            ->callAction('issue', $this->issueData(['amount_idr' => 0]))
            ->assertNotified('Harga 0: isi alasan di Catatan (Opsi lain)');

        $this->assertDatabaseCount('license_issuances', 0);

        Livewire::actingAs($this->userWith('support'))->test(ListLicenseIssuances::class)
            ->callAction('issue', $this->issueData(['amount_idr' => 0, 'notes' => 'Hadiah untuk reviewer']));

        $this->assertDatabaseHas('license_issuances', ['order_ref' => 'WA-NEW', 'amount_idr' => 0]);
    }

    public function test_a_gateway_order_counts_as_verified_without_a_proof(): void
    {
        $this->workerIssues();

        $row = app(IssueLicense::class)->handle(new IssueRequest(email: 'new@example.test', name: 'New', months: 12, orderRef: 'GW-1', source: 'midtrans'));

        $this->assertSame('verified', $row->fresh()->paymentState());
        $this->assertFalse(LicenseIssuance::needsReview()->exists());
    }

    public function test_support_can_attach_a_reference_and_a_proof_file(): void
    {
        $row = $this->row();
        $support = $this->userWith('support');

        Livewire::actingAs($support)->test(ListLicenseIssuances::class)
            ->callAction(TestAction::make('attachProof')->table($row), ['payment_ref' => 'TRX-9', 'payment_proof' => UploadedFile::fake()->image('bukti.png')])
            ->assertHasNoFormErrors();

        $fresh = $row->fresh();
        $this->assertSame('TRX-9', $fresh->payment_ref);
        $this->assertNotNull($fresh->payment_proof_path);
        Storage::disk('local')->assertExists($fresh->payment_proof_path);
        $this->assertSame('waiting', $fresh->paymentState());
        $this->assertDatabaseHas('license_issuance_events', ['license_issuance_id' => $row->id, 'event' => 'payment_attached', 'user_id' => $support->id]);
    }

    public function test_attaching_nothing_is_refused(): void
    {
        $row = $this->row();

        Livewire::actingAs($this->userWith('support'))->test(ListLicenseIssuances::class)
            ->callAction(TestAction::make('attachProof')->table($row), [])
            ->assertNotified('Isi referensi atau unggah berkas');

        $this->assertDatabaseMissing('license_issuance_events', ['event' => 'payment_attached']);
    }

    public function test_only_an_admin_can_verify_a_payment(): void
    {
        $row = $this->row(['payment_ref' => 'TRX-9']);

        Livewire::actingAs($this->userWith('support'))->test(ListLicenseIssuances::class)
            ->assertActionHidden(TestAction::make('verifyPayment')->table($row));

        $admin = $this->userWith('admin');
        Livewire::actingAs($admin)->test(ListLicenseIssuances::class)
            ->callAction(TestAction::make('verifyPayment')->table($row));

        $fresh = $row->fresh();
        $this->assertSame('verified', $fresh->paymentState());
        $this->assertSame($admin->id, $fresh->payment_verified_by);
        $this->assertDatabaseHas('license_issuance_events', ['license_issuance_id' => $row->id, 'event' => 'payment_verified', 'user_id' => $admin->id]);
        $this->assertFalse(LicenseIssuance::needsReview()->whereKey($row->id)->exists());
    }

    public function test_the_review_filter_lists_only_unverified_manual_licenses(): void
    {
        $open = $this->row();
        $verified = $this->row(['payment_verified_at' => now()]);
        $gateway = $this->row(['source' => 'midtrans']);

        Livewire::actingAs($this->userWith('admin'))->test(ListLicenseIssuances::class)
            ->filterTable('needs_review')
            ->assertCanSeeTableRecords([$open])
            ->assertCanNotSeeTableRecords([$verified]);

        $this->assertTrue($gateway->payment_verified_at === null); // gateway rows here are hand-made, not issued through the action
    }

    public function test_the_navigation_badge_counts_the_queue_for_admins_only(): void
    {
        $this->row();
        $this->row(['payment_verified_at' => now()]);

        $this->actingAs($this->userWith('admin'));
        $this->assertSame('1', LicenseIssuanceResource::getNavigationBadge());

        $this->actingAs($this->userWith('support'));
        $this->assertNull(LicenseIssuanceResource::getNavigationBadge());
    }

    public function test_the_proof_is_served_only_through_a_signed_link_to_permission_holders(): void
    {
        Storage::disk('local')->put('license-proofs/bukti.png', 'PNGDATA');
        $row = $this->row(['payment_proof_path' => 'license-proofs/bukti.png']);
        $signed = fn () => URL::temporarySignedRoute('license.proof', now()->addMinutes(5), ['issuance' => $row->id]);

        $this->actingAs($this->userWith('support'))->get($signed())->assertOk();
        $this->actingAs($this->userWith('learner'))->get($signed())->assertForbidden();
        $this->actingAs($this->userWith('support'))->get(route('license.proof', $row))->assertForbidden(); // unsigned
        auth()->logout();
        $this->get($signed())->assertRedirect(); // guest is sent to login
    }

    public function test_proofs_follow_the_configured_disk_for_upload_and_viewing(): void
    {
        config(['filesystems.proofs_disk' => 'offsite']);
        Storage::fake('offsite');
        $row = $this->row();

        Livewire::actingAs($this->userWith('support'))->test(ListLicenseIssuances::class)
            ->callAction(TestAction::make('attachProof')->table($row), ['payment_proof' => UploadedFile::fake()->image('bukti.png')])
            ->assertHasNoFormErrors();

        $path = $row->fresh()->payment_proof_path;
        Storage::disk('offsite')->assertExists($path);
        Storage::disk('local')->assertMissing($path);

        $this->actingAs($this->userWith('support'))
            ->get(URL::temporarySignedRoute('license.proof', now()->addMinutes(5), ['issuance' => $row->id]))
            ->assertOk();
    }

    public function test_a_missing_proof_file_answers_404(): void
    {
        $row = $this->row(['payment_proof_path' => 'license-proofs/gone.png']);

        $this->actingAs($this->userWith('admin'))
            ->get(URL::temporarySignedRoute('license.proof', now()->addMinutes(5), ['issuance' => $row->id]))
            ->assertNotFound();
    }

    public function test_the_privacy_page_mentions_payment_proofs_and_buyer_data(): void
    {
        $this->get('/privasi')->assertSee('Bukti pembayaran')->assertSee('server lisensi ARTi dan di aplikasi ARTi');
    }
}
