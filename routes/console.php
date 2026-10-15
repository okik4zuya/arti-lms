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
