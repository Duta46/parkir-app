<?php

namespace App\Console\Commands;

use App\Models\GeneralQRCode;
use App\Services\QRCodeService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateDailyGeneralQRCodeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parkir:generate-daily-qr';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate daily general QR code automatically';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting daily general QR code generation...');

        $qrCodeService = app(\App\Services\QRCodeService::class);

        try {
            // Cek apakah sudah ada QR code umum untuk hari ini
            $todayQRCode = GeneralQRCode::whereDate('date', Carbon::today())->first();

            if ($todayQRCode) {
                $this->info('QR code for today already exists: ' . $todayQRCode->code);
                return;
            }

            // Generate QR code umum harian untuk hari ini
            $generalQRCode = $qrCodeService->generateDailyGeneralQRCode();

            $this->info('Successfully generated daily general QR code: ' . $generalQRCode->code . ' (expires at: ' . $generalQRCode->expires_at->format('Y-m-d H:i:s') . ')');

        } catch (\Exception $e) {
            $this->error('Error generating daily general QR code: ' . $e->getMessage());
            Log::error('Error in GenerateDailyGeneralQRCodeCommand: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
