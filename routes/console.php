<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// The Worker wins on any difference; this also retries pending issues and re-checks stored keys.
Schedule::command('licenses:sync')->dailyAt('03:00')->withoutOverlapping();

// Welcome emails for the mailing list; a no-op until MAIL_SERIES_ENABLED=true.
Schedule::command('subscribers:send-series')->hourly()->withoutOverlapping();

// Catch payments whose webhook never arrived, give back places of lapsed orders and restart stuck fulfilment.
Schedule::command('orders:reconcile')->everyTenMinutes()->withoutOverlapping();

// Failed jobs may hold a one-time set-password link; do not keep them longer than a week.
Schedule::command('queue:prune-failed --hours=168')->daily();

// Unpaid orders keep a name and an email for nothing; the privacy page promises they go within a year.
Schedule::command('orders:prune')->dailyAt('03:30');
