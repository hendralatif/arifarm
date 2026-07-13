<?php

namespace App\Console\Commands;

use App\Models\FeedingSchedule;
use App\Models\FeedStock;
use App\Models\Goat;
use App\Models\GoatFeeding;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoRecordDailyFeeding extends Command
{
    /**
     * The name and signature of the console command.
     * Usage: php artisan feedings:auto-record
     * Usage with specific session: php artisan feedings:auto-record --session=pagi
     * Usage with specific date: php artisan feedings:auto-record --date=2026-07-08
     */
    protected $signature = 'feedings:auto-record
                            {--session= : Specific session to record (pagi/sore). If omitted, records all sessions for today.}
                            {--date= : Specific date (Y-m-d). Defaults to today.}
                            {--force : Force re-record even if already recorded today}';

    protected $description = 'Otomatis mencatat log pemberian pakan harian berdasarkan jadwal pakan permanen yang sudah dikonfigurasi admin.';

    public function handle(): int
    {
        $targetDate = $this->option('date')
            ? Carbon::createFromFormat('Y-m-d', $this->option('date'))
            : today();

        // Map English day names to Indonesian schedule keys
        $dayMap = [
            'Monday'    => 'senin',
            'Tuesday'   => 'selasa',
            'Wednesday' => 'rabu',
            'Thursday'  => 'kamis',
            'Friday'    => 'jumat',
            'Saturday'  => 'sabtu',
            'Sunday'    => 'minggu',
        ];

        $todayKey = $dayMap[$targetDate->format('l')] ?? 'senin';
        $targetSession = $this->option('session');

        $this->info("📅 Tanggal: {$targetDate->format('d/m/Y')} ({$todayKey})");

        // Build schedule query
        $query = FeedingSchedule::with(['feedStock1', 'feedStock2'])
            ->where('day_of_week', $todayKey);

        if ($targetSession) {
            $query->where('session', $targetSession);
        }

        $schedules = $query->get();

        if ($schedules->isEmpty()) {
            $this->warn("⚠️  Tidak ada jadwal pakan yang dikonfigurasi untuk hari {$todayKey}" .
                ($targetSession ? " sesi {$targetSession}" : '') . '. Tidak ada log yang dibuat.');
            return self::SUCCESS;
        }

        // Get active goat count
        $totalGoats = Goat::where('status', 'available')->count();

        if ($totalGoats === 0) {
            $this->warn('⚠️  Tidak ada kambing aktif (status: available). Log tidak akan dibuat.');
            return self::SUCCESS;
        }

        $recorded = 0;
        $skipped  = 0;

        foreach ($schedules as $schedule) {
            $session = $schedule->session;

            // Check if already recorded for this date + session (prevent duplicates)
            if (!$this->option('force')) {
                $exists = GoatFeeding::whereDate('feeding_date', $targetDate)
                    ->where('session', $session)
                    ->where('notes', 'like', '%[AUTO]%')
                    ->exists();

                if ($exists) {
                    $this->line("  ↳ <comment>Sesi {$session} ({$targetDate->format('d/m/Y')}) sudah tercatat otomatis sebelumnya. Dilewati.</comment>");
                    $skipped++;
                    continue;
                }
            }

            // Calculate quantities based on type
            $qty1 = 0;
            $qty2 = 0;

            if ($schedule->feed_stock_1_id) {
                $qty1 = $schedule->qty_type_1 === 'per_goat'
                    ? (float) $schedule->quantity_1_kg * $totalGoats
                    : (float) $schedule->quantity_1_kg;
            }

            if ($schedule->feed_stock_2_id) {
                $qty2 = $schedule->qty_type_2 === 'per_goat'
                    ? (float) $schedule->quantity_2_kg * $totalGoats
                    : (float) $schedule->quantity_2_kg;
            }

            // Deduct from stock 1
            if ($schedule->feedStock1 && $qty1 > 0) {
                if ($schedule->feedStock1->stock_kg < $qty1) {
                    $this->error("  ✗ Stok {$schedule->feedStock1->name} tidak mencukupi! " .
                        "Dibutuhkan: {$qty1} kg, Tersedia: {$schedule->feedStock1->stock_kg} kg. " .
                        "Sesi {$session} TIDAK dicatat.");
                    continue;
                }
                $schedule->feedStock1->decrement('stock_kg', $qty1);
            }

            // Deduct from stock 2
            if ($schedule->feedStock2 && $qty2 > 0) {
                if ($schedule->feedStock2->stock_kg < $qty2) {
                    $this->error("  ✗ Stok {$schedule->feedStock2->name} tidak mencukupi! " .
                        "Dibutuhkan: {$qty2} kg, Tersedia: {$schedule->feedStock2->stock_kg} kg. " .
                        "Sesi {$session} TIDAK dicatat.");
                    // Restore stock 1 if already deducted
                    if ($schedule->feedStock1 && $qty1 > 0) {
                        $schedule->feedStock1->increment('stock_kg', $qty1);
                    }
                    continue;
                }
                $schedule->feedStock2->decrement('stock_kg', $qty2);
            }

            // Determine feeding time based on session
            $feedingTime = $session === 'pagi' ? '07:00:00' : '16:00:00';

            // Create the feeding log
            GoatFeeding::create([
                'feeding_date'    => $targetDate->format('Y-m-d'),
                'feeding_time'    => $feedingTime,
                'feed_stock_1_id' => $schedule->feed_stock_1_id,
                'feed_stock_2_id' => $schedule->feed_stock_2_id,
                'feed_type_1'     => $schedule->feedStock1?->name,
                'feed_type_2'     => $schedule->feedStock2?->name,
                'quantity_1_kg'   => $qty1,
                'quantity_2_kg'   => $qty2,
                'goat_count'      => $totalGoats,
                'session'         => $session,
                'notes'           => '[AUTO] Dicatat otomatis berdasarkan jadwal pakan permanen.' .
                    ($schedule->notes ? ' Catatan jadwal: ' . $schedule->notes : ''),
                'recorded_by'     => null, // null = sistem otomatis
            ]);

            $this->info("  ✓ Sesi {$session}: " .
                ($schedule->feedStock1 ? $schedule->feedStock1->name . " {$qty1} kg" : '') .
                ($schedule->feedStock2 ? " + " . $schedule->feedStock2->name . " {$qty2} kg" : '') .
                " — {$totalGoats} ekor kambing.");

            $recorded++;
        }

        $this->newLine();
        $this->info("✅ Selesai! {$recorded} sesi dicatat otomatis, {$skipped} dilewati (sudah tercatat).");

        return self::SUCCESS;
    }
}
