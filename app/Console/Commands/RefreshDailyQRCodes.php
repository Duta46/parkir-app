<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\QRCodeService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RefreshDailyQRCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parkir:refresh-qr-harian';

    /**
     * Deskripsi perintah konsol.
     *
     * @var string
     */
    protected $description = 'Generate QR code harian untuk semua pengguna';

    /**
     * Execute the console command.
     */
    public function handle(QRCodeService $qrCodeService)
    {
        $users = User::all();

        foreach ($users as $user) {
            // Generate QR code for today
            $qrCodeService->generateDailyQRCode($user, Carbon::today()->format('Y-m-d'));
        }

        $this->info('Daily QR codes have been generated for all users.');
    }
}
