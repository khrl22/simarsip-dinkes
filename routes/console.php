<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Perbarui status Aktif/Inaktif arsip tiap hari berdasarkan JRA.
// Jalankan scheduler: `php artisan schedule:work` (lokal) atau cron `schedule:run` tiap menit (server).
\Illuminate\Support\Facades\Schedule::command('arsip:sinkron-retensi')->dailyAt('00:05');
