<?php

use App\Console\Commands\AutoRecordDailyFeeding;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Jadwal Otomatis Pemberian Pakan Harian
|--------------------------------------------------------------------------
| Sistem akan otomatis mencatat log pemberian pakan setiap hari berdasarkan
| jadwal pakan permanen yang sudah dikonfigurasi admin. Admin cukup mengatur
| jadwal sekali, sistem akan terus mencatat selamanya sampai jadwal diubah.
|
| Untuk mengaktifkan, tambahkan ke Windows Task Scheduler atau cron (Linux):
|   * * * * * php artisan schedule:run >> /dev/null 2>&1
|
*/

// Sesi Pagi: Dicatat otomatis setiap hari pukul 07:00
Schedule::command('feedings:auto-record --session=pagi')
    ->dailyAt('07:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/auto-feeding.log'))
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('[AUTO-FEEDING] Sesi pagi berhasil dicatat otomatis.');
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('[AUTO-FEEDING] Gagal mencatat sesi pagi otomatis!');
    });

// Sesi Sore: Dicatat otomatis setiap hari pukul 16:00
Schedule::command('feedings:auto-record --session=sore')
    ->dailyAt('16:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/auto-feeding.log'))
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('[AUTO-FEEDING] Sesi sore berhasil dicatat otomatis.');
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('[AUTO-FEEDING] Gagal mencatat sesi sore otomatis!');
    });
