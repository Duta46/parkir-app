<?php

namespace App\Console;

use App\Console\Commands\RefreshDailyQRCodes;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Jalankan perintah refresh setiap hari pukul 00:01
        $schedule->command('parkir:refresh-qr-harian')->dailyAt('00:01');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}