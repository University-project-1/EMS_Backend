<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('cleanup:unverified')->daily();
Schedule::command('app:auto-deploy')->everyTwoHours();
Schedule::command('notifications:send-event-reminders')->everyMinute();
Schedule::command('events:reject-expired-pending')
    ->everyFiveMinutes()
    ->withoutOverlapping(10)
    ->onOneServer();
