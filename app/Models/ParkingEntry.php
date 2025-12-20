<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ParkingEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_parkir',
        'user_id',
        'qr_code_id',
        'general_qr_code_id',
        'entry_time',
        'entry_location',
        'vehicle_type',
        'vehicle_plate_number',
    ];

    protected $casts = [
        'entry_time' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function qrCode(): BelongsTo
    {
        return $this->belongsTo(\App\Models\QrCode::class);
    }

    public function generalQRCode(): BelongsTo
    {
        return $this->belongsTo(\App\Models\GeneralQRCode::class, 'general_qr_code_id');
    }

    public function parkingExit(): HasOne
    {
        return $this->hasOne(\App\Models\ParkingExit::class, 'parking_entry_id');
    }

    public function parkingTransaction(): HasOne
    {
        return $this->hasOne(\App\Models\ParkingTransaction::class);
    }
}
