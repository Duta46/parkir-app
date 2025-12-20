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

        // Generate daily general QR code automatically at midnight
        $schedule->command('parkir:generate-daily-qr')->dailyAt('00:05');
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