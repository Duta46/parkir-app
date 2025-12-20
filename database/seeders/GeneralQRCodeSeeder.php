<?php

namespace Database\Seeders;

use App\Models\GeneralQRCode;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GeneralQRCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Generate a general QR code for today
        $today = Carbon::today();
        $formattedDate = $today->format('dmy');
        $code = 'GENERAL-' . $formattedDate . '-' . Str::random(8);

        GeneralQRCode::create([
            'code' => $code,
            'date' => $today,
            'expires_at' => $today->endOfDay(), // Expire at end of day
        ]);

        $this->command->info('General QR code for today created: ' . $code);
    }
}
