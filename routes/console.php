<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// The Worker wins on any difference; this also retries pending issues and re-checks stored keys.
Schedule::command('licenses:sync')->dailyAt('03:00')->withoutOverlapping();
