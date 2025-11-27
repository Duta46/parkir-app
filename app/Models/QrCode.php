<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QrCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'code',
        'date',
        'is_used',
        'expires_at',
    ];

    protected $casts = [
        'date' => 'date',
        'is_used' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function parkingEntries()
    {
        return $this->hasMany(\App\Models\ParkingEntry::class);
    }
}
