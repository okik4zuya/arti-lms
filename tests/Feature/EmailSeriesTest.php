<?php

namespace Tests\Feature;

use App\Mail\SeriesMail;
use App\Models\Subscriber;
use App\Support\EmailSeries;
use App\Support\MailText;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailSeriesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['site.mail.series_enabled' => true, 'site.mail.series_batch' => 100]);
        Mail::fake();
    }

    private function subscriber(string $email, array $over = [], int $confirmedDaysAgo = 11): Subscriber
    {
        $row = Subscriber::create(['email' => $email, 'source' => 'baca-2', 'consented_at' => now()->subDays($confirmedDaysAgo + 1)]);
        $row->forceFill($over + ['confirmed_at' => now()->subDays($confirmedDaysAgo)])->save();

        return $row->fresh();
    }

    public function test_the_series_is_off_by_default(): void
    {
        config(['site.mail.series_enabled' => false]);
        $this->subscriber('a@example.test');

        $this->artisan('subscribers:send-series')->expectsOutputToContain('off')->assertSuccessful();

        Mail::assertNothingOutgoing();
    }

    public function test_step_timing_follows_the_days_since_confirmation(): void
    {
        $this->assertNull(EmailSeries::dueStep($this->subscriber('a@example.test', [], 0)));
        $this->assertSame(1, EmailSeries::dueStep($this->subscriber('b@example.test', [], 1)));

        $stepTwo = $this->subscriber('c@example.test', ['series_step' => 1, 'series_sent_at' => now()->subDays(2)], 2);
        $this->assertNull(EmailSeries::dueStep($stepTwo));            // 3 days are needed, 2 passed
        $stepTwo->forceFill(['confirmed_at' => now()->subDays(3)])->save();
        $this->assertSame(2, EmailSeries::dueStep($stepTwo->fresh()));

        $done = $this->subscriber('d@example.test', ['series_step' => EmailSeries::count(), 'series_sent_at' => now()->subDays(5)]);
        $this->assertNull(EmailSeries::dueStep($done));
    }

    public function test_it_sends_one_step_at_a_time_even_when_several_are_overdue(): void
    {
        $late = $this->subscriber('late@example.test', [], 30);

        $this->artisan('subscribers:send-series')->assertSuccessful();
        Mail::assertQueued(SeriesMail::class, 1);
        $this->assertSame(1, $late->fresh()->series_step);

        // Running again straight away does nothing: the gap between two emails is at least a day.
        $this->artisan('subscribers:send-series')->assertSuccessful();
        Mail::assertQueued(SeriesMail::class, 1);

        $this->travel(21)->hours();
        $this->artisan('subscribers:send-series')->assertSuccessful();
        Mail::assertQueued(SeriesMail::class, 2);
        $this->assertSame(2, $late->fresh()->series_step);
    }

    public function test_the_whole_series_runs_in_order_and_then_stops(): void
    {
        $person = $this->subscriber('flow@example.test', [], 0);
        $steps = [];

        foreach (range(1, 14) as $day) {
            $this->travel(1)->days();
            $this->artisan('subscribers:send-series')->assertSuccessful();
        }

        Mail::assertQueued(SeriesMail::class, function (SeriesMail $mail) use (&$steps) {
            $steps[] = $mail->step;

            return true;
        });
        $this->assertSame([1, 2, 3, 4], $steps);
        $this->assertSame(4, $person->fresh()->series_step);
    }

    public function test_buyers_unconfirmed_and_unsubscribed_people_get_nothing(): void
    {
        $this->subscriber('buyer@example.test', ['buyer_at' => now()]);
        $this->subscriber('pending@example.test', ['confirmed_at' => null]);
        $this->subscriber('gone@example.test', ['unsubscribed_at' => now()]);
        $ok = $this->subscriber('ok@example.test');

        $this->artisan('subscribers:send-series')->assertSuccessful();

        Mail::assertQueued(SeriesMail::class, 1);
        Mail::assertQueued(SeriesMail::class, fn ($mail) => $mail->hasTo('ok@example.test'));
        $this->assertSame(1, $ok->fresh()->series_step);
        $this->assertSame(0, Subscriber::where('email', 'buyer@example.test')->value('series_step'));
    }

    public function test_someone_who_buys_midway_stops_receiving_the_series(): void
    {
        $person = $this->subscriber('mid@example.test', ['series_step' => 2, 'series_sent_at' => now()->subDays(3)], 7);
        Subscriber::markBuyer('mid@example.test');

        $this->artisan('subscribers:send-series')->assertSuccessful();

        Mail::assertNothingOutgoing();
        $this->assertSame(2, $person->fresh()->series_step);
    }

    public function test_the_batch_limit_caps_one_run_and_the_rest_waits_for_the_next(): void
    {
        foreach (range(1, 5) as $i) {
            $this->subscriber("p{$i}@example.test");
        }

        $this->artisan('subscribers:send-series', ['--limit' => 2])->assertSuccessful();
        Mail::assertQueued(SeriesMail::class, 2);

        $this->artisan('subscribers:send-series', ['--limit' => 10])->assertSuccessful();
        Mail::assertQueued(SeriesMail::class, 5);
    }

    public function test_a_queue_outage_gives_the_step_back(): void
    {
        $person = $this->subscriber('q@example.test');
        Mail::shouldReceive('to')->andThrow(new \RuntimeException('redis down'));

        $this->artisan('subscribers:send-series')->assertSuccessful();

        $this->assertSame(0, $person->fresh()->series_step);
        $this->assertNull($person->fresh()->series_sent_at);
    }

    public function test_every_email_renders_with_a_text_twin_the_footer_and_the_unsubscribe_headers(): void
    {
        $person = $this->subscriber('render@example.test');

        foreach (range(1, EmailSeries::count()) as $step) {
            $mail = new SeriesMail($person, $step);
            $html = $mail->render();

            $this->assertStringContainsString(EmailSeries::subject($step), $mail->envelope()->subject);
            $this->assertStringContainsString('berhenti', mb_strtolower($html), "step {$step} footer");
            $this->assertStringNotContainsString('kami', mb_strtolower($html), "step {$step} uses 'ARTi', not 'kami'");

            $headers = $mail->headers()->text;
            $this->assertStringContainsString('berhenti/'.$person->id, $headers['List-Unsubscribe']);
            $this->assertSame('List-Unsubscribe=One-Click', $headers['List-Unsubscribe-Post']);
        }
    }

    public function test_the_offer_email_follows_the_real_offer_numbers(): void
    {
        $person = $this->subscriber('offer@example.test');
        config(['site.offer.launch_sold' => 40, 'site.offer.launch_quota' => 100, 'site.offer.price_launch' => 99000, 'site.offer.price_normal' => 199000]);

        $html = (new SeriesMail($person, 4))->render();
        $this->assertStringContainsString('Rp99.000', $html);
        $this->assertStringContainsString('tersisa 60 dari 100', $html);
        $this->assertStringContainsString('Rp199.000', $html);
        $this->assertStringContainsString('7 hari', $html);
        $this->assertStringContainsString('tidak menjamin naskah diterima', $html);

        config(['site.offer.launch_sold' => 100]);
        $html = (new SeriesMail($person, 4))->render();
        $this->assertStringNotContainsString('Rp99.000', $html);
        $this->assertStringContainsString('Harga perintis sudah berakhir', $html);

        config(['site.offer.launch_sold' => null]);
        $html = (new SeriesMail($person, 4))->render();
        $this->assertStringNotContainsString('tersisa', $html);   // no invented counter when none is set
    }

    public function test_the_only_email_with_a_sales_pitch_is_the_last_one(): void
    {
        $person = $this->subscriber('pitch@example.test');

        foreach ([1, 2, 3] as $step) {
            $html = (new SeriesMail($person, $step))->render();
            $this->assertStringNotContainsString('Rp99.000', $html, "step {$step}");
            $this->assertStringNotContainsString('Beli lewat WhatsApp', $html, "step {$step}");
        }
    }

    public function test_the_footer_date_is_in_indonesian_and_the_text_twin_has_no_template_indentation(): void
    {
        $person = $this->subscriber('date@example.test');
        $person->forceFill(['consented_at' => \Carbon\Carbon::create(2026, 10, 3)])->save();

        $mail = new SeriesMail($person->fresh(), 4);
        $html = $mail->render();

        $this->assertStringContainsString('3 Oktober 2026', $html);
        $this->assertStringContainsString('/#harga', $html);

        $text = MailText::fromHtml($html);
        $this->assertDoesNotMatchRegularExpression('/^[ \t]+\S/m', $text);
    }

    public function test_the_text_twin_keeps_links_and_drops_markup(): void
    {
        $text = MailText::fromHtml('<style>p{color:red}</style><p>Halo <strong>Anda</strong></p><ul><li>satu</li></ul><a href="https://x.test/a?b=1&amp;c=2">Klik</a>');

        $this->assertStringNotContainsString('<', $text);
        $this->assertStringNotContainsString('color:red', $text);
        $this->assertStringContainsString('Halo Anda', $text);
        $this->assertStringContainsString('- satu', $text);
        $this->assertStringContainsString('Klik (https://x.test/a?b=1&c=2)', $text);
    }
}
