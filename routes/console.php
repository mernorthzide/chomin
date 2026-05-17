<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('orders:cancel-expired')->hourly();
Schedule::command('carts:send-recovery')->hourly();
Schedule::command('stock:send-alerts')->everyFifteenMinutes();
Schedule::command('reviews:request')->dailyAt('10:00');
