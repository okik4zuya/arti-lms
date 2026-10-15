<?php

namespace Tests\Feature;

use App\Filament\Resources\BroadcastResource;
use App\Filament\Resources\BroadcastResource\Pages\CreateBroadcast;
use App\Filament\Resources\BroadcastResource\Pages\EditBroadcast;
use App\Filament\Resources\BroadcastResource\Pages\ListBroadcasts;
use App\Jobs\SendBroadcast;
use App\Mail\BroadcastMail;
use App\Models\Broadcast;
use App\Models\Subscriber;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use Tests\TestCase;

class BroadcastTest extends TestCase
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
        $user = User::factory()->create(['email' => "{$role}@example.test"]);
        $user->assignRole($role);
        $this->actingAs($user);

        return $user;
    }

    private function subscriber(string $email, array $over = []): Subscriber
    {
        $row = Subscriber::create(['email' => $email, 'source' => 'baca-2', 'consented_at' => now()->subDay()]);
        $row->forceFill($over + ['confirmed_at' => now()->subDay()])->save();

        return $row->fresh();
    }

    private function broadcast(array $over = []): Broadcast
    {
        $broadcast = new Broadcast(['subject' => 'Bab baru', 'body' => "Halo **Anda**.\n\n[Baca](https://example.test/a)", 'audience' => Broadcast::NON_BUYERS]);
        $broadcast->forceFill($over)->save();

        return $broadcast->fresh();
    }

    private function people(): array
    {
        return [
            'ok' => $this->subscriber('ok@example.test'),
            'buyer' => $this->subscriber('buyer@example.test', ['buyer_at' => now()]),
            'pending' => $this->subscriber('pending@example.test', ['confirmed_at' => null]),
            'gone' => $this->subscriber('gone@example.test', ['unsubscribed_at' => now()]),
        ];
    }

    public function test_the_job_mails_only_confirmed_non_buyers_for_a_sales_audience(): void
    {
        Mail::fake();
        $this->people();
        $broadcast = $this->broadcast();

        (new SendBroadcast($broadcast->id))->handle();

        Mail::assertQueued(BroadcastMail::class, 1);
        Mail::assertQueued(BroadcastMail::class, fn ($mail) => $mail->hasTo('ok@example.test'));
        $broadcast->refresh();
        $this->assertSame(Broadcast::SENT, $broadcast->status);
        $this->assertSame(1, $broadcast->recipients_count);
        $this->assertNotNull($broadcast->sent_at);
    }

    public function test_a_general_audience_includes_buyers_but_never_unconfirmed_or_unsubscribed_people(): void
    {
        Mail::fake();
        $this->people();
        $broadcast = $this->broadcast(['audience' => Broadcast::ALL_ACTIVE]);

        (new SendBroadcast($broadcast->id))->handle();

        Mail::assertQueued(BroadcastMail::class, 2);
        Mail::assertQueued(BroadcastMail::class, fn ($mail) => $mail->hasTo('buyer@example.test'));
        Mail::assertNotQueued(BroadcastMail::class, fn ($mail) => $mail->hasTo('pending@example.test') || $mail->hasTo('gone@example.test'));
    }

    public function test_running_the_job_again_never_mails_anyone_twice(): void
    {
        Mail::fake();
        $people = $this->people();
        $broadcast = $this->broadcast();

        (new SendBroadcast($broadcast->id))->handle();
        (new SendBroadcast($broadcast->id))->handle();

        Mail::assertQueued(BroadcastMail::class, 1);

        // A crashed earlier run: one person is already recorded, so a resumed run skips them and mails the rest.
        $other = $this->broadcast(['subject' => 'Lain']);
        $late = $this->subscriber('late@example.test');
        DB::table('broadcast_deliveries')->insert(['broadcast_id' => $other->id, 'subscriber_id' => $people['ok']->id, 'queued_at' => now()]);
        Mail::fake();

        (new SendBroadcast($other->id))->handle();

        Mail::assertQueued(BroadcastMail::class, 1);
        Mail::assertQueued(BroadcastMail::class, fn ($mail) => $mail->hasTo('late@example.test'));
        $this->assertSame(2, $other->fresh()->recipients_count);
    }

    public function test_a_queue_outage_stops_the_job_and_lets_a_later_run_pick_the_person_up(): void
    {
        $person = $this->subscriber('ok@example.test');
        $broadcast = $this->broadcast(['status' => Broadcast::SENDING]);
        Mail::shouldReceive('to')->andThrow(new \RuntimeException('redis down'));

        try {
            (new SendBroadcast($broadcast->id))->handle();
            $this->fail('the outage should surface');
        } catch (\RuntimeException) {
        }

        $this->assertSame(Broadcast::SENDING, $broadcast->fresh()->status);
        $this->assertSame(0, DB::table('broadcast_deliveries')->count());
        $this->assertTrue($broadcast->fresh()->recipients()->whereKey($person->id)->exists());
    }

    public function test_the_mail_renders_markdown_with_the_footer_and_unsubscribe_headers(): void
    {
        $person = $this->subscriber('ok@example.test');
        $mail = new BroadcastMail($person, $this->broadcast());
        $html = $mail->render();

        $this->assertStringContainsString('<strong>Anda</strong>', $html);
        $this->assertStringContainsString('href="https://example.test/a"', $html);
        $this->assertStringContainsString('Berhenti berlangganan', $html);
        $this->assertSame('Bab baru', $mail->envelope()->subject);
        $this->assertStringContainsString('berhenti/'.$person->id, $mail->headers()->text['List-Unsubscribe']);
    }

    public function test_a_test_copy_is_marked_and_has_no_working_unsubscribe_link(): void
    {
        $mail = new BroadcastMail(null, $this->broadcast(), test: true);

        $this->assertSame('[UJI] Bab baru', $mail->envelope()->subject);
        $this->assertStringContainsString('tautan berhenti tidak aktif', $mail->render());
        $this->assertSame([], $mail->headers()->text);
    }

    public function test_only_an_admin_may_open_the_broadcast_pages(): void
    {
        $this->as('support');
        $this->assertFalse(BroadcastResource::canViewAny());
        $this->get('/jamrud/broadcasts')->assertForbidden();

        $this->as('admin');
        $this->get('/jamrud/broadcasts')->assertOk();
        $this->get('/jamrud/broadcasts/create')->assertOk();
    }

    public function test_an_admin_can_write_a_draft_and_it_records_the_author(): void
    {
        $admin = $this->as('admin');

        Livewire::test(CreateBroadcast::class)
            ->fillForm(['subject' => 'Batch harga berikutnya', 'audience' => Broadcast::NON_BUYERS, 'body' => 'Halo.'])
            ->call('create')
            ->assertHasNoFormErrors();

        $draft = Broadcast::sole();
        $this->assertSame(Broadcast::DRAFT, $draft->status);
        $this->assertSame($admin->id, $draft->created_by);
    }

    public function test_subject_and_body_are_required(): void
    {
        $this->as('admin');

        Livewire::test(CreateBroadcast::class)
            ->fillForm(['subject' => '', 'body' => ''])
            ->call('create')
            ->assertHasFormErrors(['subject' => 'required', 'body' => 'required']);
    }

    public function test_a_sent_broadcast_can_be_neither_edited_nor_deleted(): void
    {
        $this->as('admin');
        $draft = $this->broadcast();
        $sent = $this->broadcast(['status' => Broadcast::SENT, 'sent_at' => now()]);

        $this->assertTrue(BroadcastResource::canEdit($draft));
        $this->assertTrue(BroadcastResource::canDelete($draft));
        $this->assertFalse(BroadcastResource::canEdit($sent));
        $this->assertFalse(BroadcastResource::canDelete($sent));
        $this->get('/jamrud/broadcasts/'.$sent->id.'/edit')->assertForbidden();
    }

    public function test_the_table_offers_no_edit_or_delete_for_a_sent_broadcast_and_a_crafted_call_cannot_delete_it(): void
    {
        $this->as('admin');
        $draft = $this->broadcast();
        $sent = $this->broadcast(['status' => Broadcast::SENT, 'sent_at' => now()]);

        $list = Livewire::test(ListBroadcasts::class)
            ->assertTableActionVisible('delete', $draft)
            ->assertTableActionHidden('delete', $sent)
            ->assertTableActionHidden('edit', $sent);

        // Filament's own test helper refuses to run an action that is hidden for the record, which is the
        // behaviour that protects the history; the row must be there either way.
        try {
            $list->callAction(TestAction::make('delete')->table($sent));
        } catch (\PHPUnit\Framework\AssertionFailedError) {
        }

        $this->assertDatabaseHas('broadcasts', ['id' => $sent->id]);
    }

    public function test_the_test_action_queues_a_marked_copy_to_the_admin_only(): void
    {
        Mail::fake();
        $this->as('admin');
        $this->subscriber('ok@example.test');
        $draft = $this->broadcast();

        Livewire::test(ListBroadcasts::class)->callAction(TestAction::make('test')->table($draft));

        Mail::assertQueued(BroadcastMail::class, 1);
        Mail::assertQueued(BroadcastMail::class, fn ($mail) => $mail->hasTo('admin@example.test') && $mail->test === true);
        $this->assertSame(Broadcast::DRAFT, $draft->fresh()->status);
    }

    public function test_sending_marks_the_broadcast_as_sending_and_starts_the_job(): void
    {
        Queue::fake();
        $this->as('admin');
        $this->subscriber('ok@example.test');
        $draft = $this->broadcast();

        Livewire::test(ListBroadcasts::class)->callAction(TestAction::make('send')->table($draft));

        $this->assertSame(Broadcast::SENDING, $draft->fresh()->status);
        Queue::assertPushed(SendBroadcast::class, fn ($job) => $job->broadcastId === $draft->id);
    }

    public function test_sending_with_nobody_to_mail_does_nothing(): void
    {
        Queue::fake();
        $this->as('admin');
        $draft = $this->broadcast();

        Livewire::test(ListBroadcasts::class)->callAction(TestAction::make('send')->table($draft));

        $this->assertSame(Broadcast::DRAFT, $draft->fresh()->status);
        Queue::assertNothingPushed();
    }

    public function test_a_sent_broadcast_cannot_be_sent_again(): void
    {
        $this->as('admin');
        $sent = $this->broadcast(['status' => Broadcast::SENT, 'sent_at' => now()]);

        Livewire::test(ListBroadcasts::class)->assertTableActionHidden('send', $sent);
    }

    public function test_an_interrupted_broadcast_can_be_continued(): void
    {
        Queue::fake();
        $this->as('admin');
        $this->subscriber('left@example.test');
        $stuck = $this->broadcast(['status' => Broadcast::SENDING]);

        Livewire::test(ListBroadcasts::class)
            ->assertTableActionVisible('send', $stuck)
            ->callAction(TestAction::make('send')->table($stuck));

        Queue::assertPushed(SendBroadcast::class);
    }
}
