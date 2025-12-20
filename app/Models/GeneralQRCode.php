<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GeneralQRCode extends Model
{
    use HasFactory;

    protected $table = 'general_qr_codes'; 

    protected $fillable = [
        'code',
        'date',
        'expires_at',
    ];

    protected $casts = [
        'date' => 'date',
        'expires_at' => 'datetime',
    ];

    /**
     * Scope a query to only include QR codes valid for today.
     */
    public function scopeForToday($query)
    {
        return $query->whereDate('date', today());
    }

    /**
     * Scope a query to only include QR codes that are not expired.
     */
    public function scopeNotExpired($query)
    {
        return $query->where('expires_at', '>', now());
    }

    /**
     * Get the parking entries that used this general QR code.
     */
    public function parkingEntries()
    {
        return $this->hasMany(\App\Models\ParkingEntry::class, 'general_qr_code_id');
    }
}
