<?php

namespace Tests\Feature;

use App\Mail\ConfirmSubscription;
use App\Models\Subscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    private function signUp(string $email = 'dosen@example.com', string $source = 'baca-2', array $extra = [])
    {
        return $this->postJson(route('subscribe.store'), ['email' => $email, 'source' => $source] + $extra);
    }

    public function test_sign_up_stores_the_address_and_sends_the_confirmation(): void
    {
        Mail::fake();

        $this->signUp('  Dosen@Example.com ')->assertOk()->assertJson(['message' => \App\Http\Controllers\SubscriptionController::ANSWER]);

        $subscriber = Subscriber::sole();
        $this->assertSame('dosen@example.com', $subscriber->email);
        $this->assertSame('baca-2', $subscriber->source);
        $this->assertNotNull($subscriber->consented_at);
        $this->assertNull($subscriber->confirmed_at);
        $this->assertNotNull($subscriber->confirmation_sent_at);
        Mail::assertQueued(ConfirmSubscription::class, fn ($mail) => $mail->hasTo('dosen@example.com'));
    }

    public function test_the_confirmation_goes_through_the_queue_and_arrives_with_working_links(): void
    {
        $this->assertInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class, new ConfirmSubscription(new Subscriber));

        // Tests run the queue synchronously and the mailer into an array, so the job really executes.
        $this->signUp('dosen@example.com', 'baca-1')->assertOk();

        $messages = app('mailer')->getSymfonyTransport()->messages();
        $this->assertCount(1, $messages);
        $body = $messages[0]->getOriginalMessage()->toString();
        $this->assertStringContainsString('dosen@example.com', $body);
        $this->assertStringContainsString('signature=', $body);
    }

    public function test_the_confirmation_email_has_the_signed_links_and_the_chapter_link(): void
    {
        $subscriber = Subscriber::create(['email' => 'a@example.com', 'source' => 'artipdf', 'consented_at' => now()]);

        $html = (new ConfirmSubscription($subscriber))->render();

        $this->assertStringContainsString('daftar/konfirmasi/'.$subscriber->id, $html);
        $this->assertStringContainsString('berhenti/'.$subscriber->id, $html);
        $this->assertStringContainsString(route('free.start'), $html);
        $this->assertStringContainsString('Run anyway', $html);
    }

    public function test_invalid_input_is_rejected_and_nothing_is_stored(): void
    {
        Mail::fake();

        $this->signUp('not-an-email')->assertUnprocessable()->assertJsonValidationErrorFor('email');
        $this->signUp('a@example.com', 'elsewhere')->assertUnprocessable();
        $this->postJson(route('subscribe.store'), ['source' => 'baca-1'])->assertUnprocessable();

        $this->assertSame(0, Subscriber::count());
        Mail::assertNothingOutgoing();
    }

    public function test_the_honeypot_gets_the_normal_answer_but_stores_nothing(): void
    {
        Mail::fake();

        $this->signUp('bot@example.com', 'baca-1', ['website' => 'http://spam.test'])->assertOk();

        $this->assertSame(0, Subscriber::count());
        Mail::assertNothingOutgoing();
    }

    public function test_a_second_sign_up_does_not_resend_within_ten_minutes_and_answers_the_same(): void
    {
        Mail::fake();

        $first = $this->signUp()->json();
        $second = $this->signUp()->json();

        $this->assertSame($first, $second);
        $this->assertSame(1, Subscriber::count());
        Mail::assertQueued(ConfirmSubscription::class, 1);

        $this->travel(11)->minutes();
        $this->signUp()->assertOk();
        Mail::assertQueued(ConfirmSubscription::class, 2);
    }

    public function test_a_confirmed_address_is_not_mailed_again(): void
    {
        Mail::fake();
        Subscriber::create(['email' => 'dosen@example.com', 'source' => 'baca-1', 'consented_at' => now()])
            ->forceFill(['confirmed_at' => now()])->save();

        $this->signUp()->assertOk();

        Mail::assertNothingOutgoing();
    }

    public function test_a_failing_mailer_does_not_break_the_sign_up(): void
    {
        Mail::shouldReceive('to')->andThrow(new \RuntimeException('smtp down'));

        $this->signUp()->assertOk();

        $this->assertNull(Subscriber::sole()->confirmation_sent_at);
    }

    public function test_the_plain_form_post_redirects_back_with_the_answer_and_no_json_needed(): void
    {
        Mail::fake();

        $this->from(route('free.show', '02-solusi-parsial'))
            ->post(route('subscribe.store'), ['email' => 'dosen@example.com', 'source' => 'baca-2'])
            ->assertRedirect(route('free.show', '02-solusi-parsial').'#daftar')
            ->assertSessionHas('subscribe.source', 'baca-2');
    }

    public function test_confirming_with_a_valid_signed_link_activates_the_address(): void
    {
        $subscriber = Subscriber::create(['email' => 'a@example.com', 'source' => 'baca-3', 'consented_at' => now()]);

        $this->get(URL::temporarySignedRoute('subscribe.confirm', now()->addDay(), ['subscriber' => $subscriber]))
            ->assertOk()->assertSee('terkonfirmasi')->assertSee('noindex', false);

        $this->assertTrue($subscriber->fresh()->isActive());
    }

    public function test_an_unsigned_expired_or_tampered_confirmation_link_is_refused(): void
    {
        $subscriber = Subscriber::create(['email' => 'a@example.com', 'source' => 'baca-3', 'consented_at' => now()]);

        $this->get(route('subscribe.confirm', $subscriber))->assertForbidden();
        $this->get(URL::temporarySignedRoute('subscribe.confirm', now()->subMinute(), ['subscriber' => $subscriber]))->assertForbidden();

        $other = Subscriber::create(['email' => 'b@example.com', 'source' => 'baca-3', 'consented_at' => now()]);
        $tampered = str_replace('/'.$subscriber->id.'?', '/'.$other->id.'?', URL::temporarySignedRoute('subscribe.confirm', now()->addDay(), ['subscriber' => $subscriber]));
        $this->get($tampered)->assertForbidden();

        $this->assertNull($subscriber->fresh()->confirmed_at);
        $this->assertNull($other->fresh()->confirmed_at);
    }

    public function test_unsubscribing_works_and_a_later_confirmation_link_does_not_revive_the_address(): void
    {
        $subscriber = Subscriber::create(['email' => 'a@example.com', 'source' => 'baca-3', 'consented_at' => now()]);
        $subscriber->forceFill(['confirmed_at' => now()])->save();

        $url = URL::signedRoute('subscribe.unsubscribe', ['subscriber' => $subscriber]);

        $this->post($url)->assertOk()->assertSee('berhenti');
        $this->assertFalse($subscriber->fresh()->isActive());

        $this->get(URL::temporarySignedRoute('subscribe.confirm', now()->addDay(), ['subscriber' => $subscriber]))->assertOk();
        $this->assertFalse($subscriber->fresh()->isActive());
    }

    public function test_opening_the_unsubscribe_link_only_shows_a_button_so_mail_scanners_cannot_unsubscribe_anyone(): void
    {
        $subscriber = Subscriber::create(['email' => 'a@example.com', 'source' => 'baca-3', 'consented_at' => now()]);
        $subscriber->forceFill(['confirmed_at' => now()])->save();
        $url = URL::signedRoute('subscribe.unsubscribe', ['subscriber' => $subscriber]);

        $this->get($url)->assertOk()
            ->assertSee('Ya, berhenti berlangganan')
            ->assertSee('action="'.$url.'"', false)
            ->assertSee('noindex', false);

        $this->assertTrue($subscriber->fresh()->isActive());
    }

    public function test_the_one_click_post_needs_a_valid_signature_but_no_csrf_token(): void
    {
        $subscriber = Subscriber::create(['email' => 'a@example.com', 'source' => 'baca-3', 'consented_at' => now()]);
        $subscriber->forceFill(['confirmed_at' => now()])->save();

        $this->post(route('subscribe.unsubscribe', $subscriber))->assertForbidden();
        $this->assertTrue($subscriber->fresh()->isActive());

        // The test client skips CSRF checks, so state the exemption itself: a mail provider's POST has no token.
        $exempt = (new \ReflectionProperty(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class, 'neverVerify'))->getValue();
        $this->assertContains('berhenti/*', $exempt);
        $this->assertNotContains('daftar', $exempt);
    }

    public function test_signing_up_again_after_unsubscribing_needs_a_new_confirmation(): void
    {
        Mail::fake();
        $subscriber = Subscriber::create(['email' => 'dosen@example.com', 'source' => 'baca-1', 'consented_at' => now()]);
        $subscriber->forceFill(['confirmed_at' => now(), 'unsubscribed_at' => now()])->save();

        $this->signUp('dosen@example.com', 'artipdf')->assertOk();

        $fresh = $subscriber->fresh();
        $this->assertNull($fresh->unsubscribed_at);
        $this->assertNull($fresh->confirmed_at);
        $this->assertSame('artipdf', $fresh->source);
        Mail::assertQueued(ConfirmSubscription::class, 1);
    }

    public function test_the_sign_up_is_throttled(): void
    {
        Mail::fake();

        foreach (range(1, 20) as $i) {
            $this->signUp("u{$i}@example.com")->assertOk();
        }

        $this->signUp('u21@example.com')->assertStatus(429);
    }

    public function test_download_clicks_do_not_use_up_the_sign_up_allowance(): void
    {
        Mail::fake();

        foreach (range(1, 25) as $i) {
            $this->get(route('artipdf.unduh'))->assertRedirect();
        }

        $this->signUp('dosen@example.com')->assertOk();
        $this->assertSame(1, Subscriber::count());
    }

    public function test_the_privacy_page_describes_the_email_list(): void
    {
        $this->get('/privasi')->assertSee('Daftar email')->assertSee('berhenti berlangganan');
    }
}
