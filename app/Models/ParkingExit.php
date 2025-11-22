<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParkingExit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'parking_entry_id',
        'exit_time',
        'exit_location',
        'parking_fee',
    ];

    protected $casts = [
        'exit_time' => 'datetime',
        'parking_fee' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function parkingEntry(): BelongsTo
    {
        return $this->belongsTo(\App\Models\ParkingEntry::class);
    }
}
