<?php

namespace Tests\Feature;

use App\Filament\Resources\SubscriberResource;
use App\Filament\Resources\SubscriberResource\Pages\ListSubscribers;
use App\Filament\Resources\SubscriberResource\Widgets\SubscriberStats;
use App\Filament\Resources\SubscriberResource\Widgets\SubscribersBySource;
use App\Mail\ConfirmSubscription;
use App\Models\DownloadCount;
use App\Models\LicenseIssuance;
use App\Models\Subscriber;
use App\Models\User;
use App\Support\SubscriberCsv;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class FilamentSubscriberTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('permissions:sync')->assertSuccessful();
        Filament::setCurrentPanel('jamrud');
    }

    private function as(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);
        $this->actingAs($user);

        return $user;
    }

    private function subscriber(string $email, string $source = 'baca-2', ?string $state = 'active'): Subscriber
    {
        $row = Subscriber::create(['email' => $email, 'source' => $source, 'consented_at' => now()]);

        match ($state) {
            'active' => $row->forceFill(['confirmed_at' => now(), 'confirmation_sent_at' => now()->subHour()])->save(),
            'unsubscribed' => $row->forceFill(['confirmed_at' => now()->subDay(), 'unsubscribed_at' => now()])->save(),
            'pending' => $row->forceFill(['confirmation_sent_at' => now()->subHour()])->save(),
            default => null,
        };

        return $row->fresh();
    }

    public function test_only_an_admin_may_open_the_list_support_and_learners_may_not(): void
    {
        $this->as('support');
        $this->assertFalse(SubscriberResource::canViewAny());
        $this->get('/jamrud/subscribers')->assertForbidden();

        $this->as('learner');
        $this->assertFalse(SubscriberResource::canViewAny());

        $this->as('admin');
        $this->assertTrue(SubscriberResource::canViewAny());
        $this->get('/jamrud/subscribers')->assertOk();
    }

    public function test_the_list_shows_status_source_and_filters_by_them(): void
    {
        $this->as('admin');
        $active = $this->subscriber('aktif@example.test', 'artipdf', 'active');
        $pending = $this->subscriber('menunggu@example.test', 'baca-1', 'pending');
        $left = $this->subscriber('pergi@example.test', 'baca-4', 'unsubscribed');

        Livewire::test(ListSubscribers::class)
            ->assertCanSeeTableRecords([$active, $pending, $left])
            ->assertSee('Menunggu konfirmasi')
            ->assertSee('ARTi PDF (unduhan)')
            ->filterTable('status', Subscriber::ACTIVE)
            ->assertCanSeeTableRecords([$active])
            ->assertCanNotSeeTableRecords([$pending, $left])
            ->filterTable('status', Subscriber::PENDING)
            ->assertCanSeeTableRecords([$pending])
            ->assertCanNotSeeTableRecords([$active, $left])
            ->filterTable('status', Subscriber::UNSUBSCRIBED)
            ->assertCanSeeTableRecords([$left])
            ->removeTableFilter('status')
            ->filterTable('source', ['baca-1', 'baca-4'])
            ->assertCanSeeTableRecords([$pending, $left])
            ->assertCanNotSeeTableRecords([$active]);
    }

    public function test_the_buyer_filter_splits_buyers_from_the_rest(): void
    {
        $this->as('admin');
        $buyer = $this->subscriber('beli@example.test');
        $buyer->forceFill(['buyer_at' => now()])->save();
        $other = $this->subscriber('belum@example.test');

        Livewire::test(ListSubscribers::class)
            ->filterTable('buyer', true)
            ->assertCanSeeTableRecords([$buyer])->assertCanNotSeeTableRecords([$other])
            ->filterTable('buyer', false)
            ->assertCanSeeTableRecords([$other])->assertCanNotSeeTableRecords([$buyer]);
    }

    public function test_resend_queues_the_confirmation_for_a_pending_address_only(): void
    {
        $this->as('admin');
        Mail::fake();
        $pending = $this->subscriber('menunggu@example.test', 'baca-1', 'pending');
        $active = $this->subscriber('aktif@example.test', 'baca-1', 'active');

        Livewire::test(ListSubscribers::class)
            ->assertTableActionVisible('resend', $pending)
            ->assertTableActionHidden('resend', $active)
            ->callAction(TestAction::make('resend')->table($pending));

        Mail::assertQueued(ConfirmSubscription::class, fn ($mail) => $mail->hasTo('menunggu@example.test'));
        $this->assertTrue($pending->fresh()->confirmation_sent_at->gt(now()->subMinute()));
    }

    public function test_resend_respects_the_ten_minute_window(): void
    {
        $this->as('admin');
        Mail::fake();
        $pending = $this->subscriber('menunggu@example.test', 'baca-1', 'pending');
        $pending->forceFill(['confirmation_sent_at' => now()->subMinutes(2)])->save();

        Livewire::test(ListSubscribers::class)->callAction(TestAction::make('resend')->table($pending));

        Mail::assertNothingOutgoing();
    }

    public function test_an_admin_can_delete_a_subscriber_for_good(): void
    {
        $this->as('admin');
        $row = $this->subscriber('hapus@example.test');

        Livewire::test(ListSubscribers::class)->callAction(TestAction::make('delete')->table($row));

        $this->assertDatabaseMissing('subscribers', ['email' => 'hapus@example.test']);
    }

    public function test_the_csv_holds_only_active_addresses_and_defuses_formulas(): void
    {
        $this->subscriber('aktif@example.test', 'artipdf', 'active');
        $this->subscriber('menunggu@example.test', 'baca-1', 'pending');
        $this->subscriber('pergi@example.test', 'baca-2', 'unsubscribed');
        $this->subscriber('+rumus@example.test', 'baca-3', 'active');
        $this->subscriber('beli@example.test', 'baca-4', 'active')->forceFill(['buyer_at' => now()])->save();

        $out = fopen('php://memory', 'w+');
        SubscriberCsv::write($out);
        rewind($out);
        $csv = stream_get_contents($out);

        $this->assertStringContainsString('email,sumber,dikonfirmasi,pembeli', $csv);
        $this->assertStringContainsString('aktif@example.test,artipdf', $csv);
        $this->assertStringNotContainsString('menunggu@example.test', $csv);
        $this->assertStringNotContainsString('pergi@example.test', $csv);
        $this->assertStringContainsString("'+rumus@example.test", $csv);
        $this->assertMatchesRegularExpression('/beli@example\.test,baca-4,[^,]+,ya/', $csv);
    }

    public function test_the_export_action_downloads_a_csv(): void
    {
        $this->as('admin');
        $this->subscriber('aktif@example.test', 'artipdf', 'active');

        $test = Livewire::test(ListSubscribers::class)
            ->callAction('export')
            ->assertFileDownloaded('daftar-email-'.now()->format('Ymd').'.csv');

        $csv = base64_decode((string) data_get($test->effects, 'download.content'));
        $this->assertStringContainsString('aktif@example.test,artipdf', $csv);
    }

    public function test_a_license_for_the_same_email_marks_the_subscriber_as_a_buyer(): void
    {
        $match = $this->subscriber('Pembeli@Example.test');
        $other = $this->subscriber('lain@example.test');

        Subscriber::markBuyer('pembeli@example.test');

        $this->assertNotNull($match->fresh()->buyer_at);
        $this->assertNull($other->fresh()->buyer_at);
    }

    public function test_sync_buyers_matches_existing_licenses_and_skips_unfinished_ones(): void
    {
        $this->as('admin');
        $bought = $this->subscriber('beli@example.test');
        $tried = $this->subscriber('coba@example.test');
        foreach ([['beli@example.test', LicenseIssuance::ACTIVE, 'A'], ['coba@example.test', LicenseIssuance::PENDING, 'B']] as [$email, $status, $ref]) {
            $license = new LicenseIssuance(['order_ref' => "WA-$ref", 'email' => $email, 'name' => 'X', 'tier' => 'individual', 'months' => 12]);
            $license->forceFill(['status' => $status, 'seats' => 1])->save();
        }

        Livewire::test(ListSubscribers::class)->callAction('syncBuyers');

        $this->assertNotNull($bought->fresh()->buyer_at);
        $this->assertNull($tried->fresh()->buyer_at);
    }

    public function test_the_widgets_count_per_status_and_per_source(): void
    {
        $this->as('admin');
        $this->subscriber('a@example.test', 'artipdf', 'active');
        $this->subscriber('b@example.test', 'artipdf', 'pending');
        $this->subscriber('c@example.test', 'baca-2', 'unsubscribed');
        DownloadCount::create(['name' => 'artipdf', 'day' => today(), 'downloads' => 4]);
        DownloadCount::create(['name' => 'artipdf', 'day' => today()->subDays(2), 'downloads' => 3]);
        DownloadCount::create(['name' => 'artipdf', 'day' => today()->subDays(30), 'downloads' => 99]);

        Livewire::test(SubscriberStats::class)
            ->assertSee('Aktif')->assertSee('Menunggu konfirmasi')->assertSee('Berhenti')
            ->assertSee('Unduhan ARTi PDF, 7 hari')->assertSee('Hari ini: 4');

        $chart = Livewire::test(SubscribersBySource::class);
        $data = (new \ReflectionMethod(SubscribersBySource::class, 'getData'))->invoke($chart->instance());
        $this->assertSame(1, $data['datasets'][0]['data'][0]);   // artipdf, active
        $this->assertSame(1, $data['datasets'][1]['data'][0]);   // artipdf, pending
        $this->assertSame(0, $data['datasets'][0]['data'][2]);   // baca-2, the unsubscribed one counts nowhere
    }

    public function test_the_widgets_stay_hidden_from_support(): void
    {
        $this->as('support');

        $this->assertFalse(SubscriberStats::canView());
        $this->assertFalse(SubscribersBySource::canView());
    }
}
