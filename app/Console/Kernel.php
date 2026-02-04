<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Masukkan kode Anda di sini
        $schedule->call(function () {
            \Illuminate\Support\Facades\Cache::flush();
            \Illuminate\Support\Facades\Log::info("Sistem: Cache telah dibersihkan otomatis.");
        })->everyFifteenMinutes();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}