<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Automated Job Pipeline Scheduler
|--------------------------------------------------------------------------
|
| Automatically synchronize active external job feeds and government
| recruitment sources periodically without overlapping.
|
*/
Schedule::command('jobs:fetch --all')
    ->twiceDaily(2, 14)
    ->withoutOverlapping(60)
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/job_fetch.log'));

Schedule::command('jobs:deactivate-stale')
    ->dailyAt('03:00')
    ->withoutOverlapping(30)
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/stale_jobs.log'));

Schedule::command('jobs:verify-urls --limit=100')
    ->dailyAt('04:00')
    ->withoutOverlapping(60)
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/url_verification.log'));
