<?php

namespace App\Console\Commands;

use App\Mail\SeriesMail;
use App\Models\Subscriber;
use App\Support\EmailSeries;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendEmailSeries extends Command
{
    protected $signature = 'subscribers:send-series {--limit= : Most people to queue in this run (default MAIL_SERIES_BATCH)}';

    protected $description = 'Queue the next welcome-series email for everyone who is due (confirmed, not unsubscribed, not a buyer)';

    public function handle(): int
    {
        if (! config('site.mail.series_enabled')) {
            $this->info('Welcome series is off (MAIL_SERIES_ENABLED=false). Nothing queued.');

            return self::SUCCESS;
        }

        $limit = (int) ($this->option('limit') ?: config('site.mail.series_batch'));
        $queued = 0;

        // Cheap filter in SQL (the first email is never due before a day after confirming); exact timing in PHP.
        $candidates = Subscriber::query()
            ->active()
            ->notBuyers()
            ->where('series_step', '<', EmailSeries::count())
            ->where('confirmed_at', '<=', now()->subDays(EmailSeries::STEPS[1]['after_days']))
            ->orderBy('id')
            ->lazyById(200);

        foreach ($candidates as $subscriber) {
            if ($queued >= $limit) {
                break;
            }

            $step = EmailSeries::dueStep($subscriber);

            if ($step === null || ! $this->claim($subscriber, $step)) {
                continue;
            }

            try {
                Mail::to($subscriber->email)->queue(new SeriesMail($subscriber, $step));
                $queued++;
            } catch (Throwable $e) {
                // Could not queue: give the step back so the next run tries again.
                Subscriber::whereKey($subscriber->id)->where('series_step', $step)->update([
                    'series_step' => $step - 1,
                    'series_sent_at' => $subscriber->series_sent_at,
                ]);
                report($e);
                $this->error("Could not queue step {$step} for subscriber {$subscriber->id}: {$e->getMessage()}");

                break; // the queue is probably down; do not hammer it
            }
        }

        $this->info("Queued {$queued} welcome email(s).");

        return self::SUCCESS;
    }

    /** Moves the person to this step in one atomic update, so two overlapping runs never queue the same email. */
    protected function claim(Subscriber $subscriber, int $step): bool
    {
        return Subscriber::whereKey($subscriber->id)
            ->where('series_step', $step - 1)
            ->update(['series_step' => $step, 'series_sent_at' => now()]) === 1;
    }
}
