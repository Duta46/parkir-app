<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParkingTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'parking_entry_id',
        'transaction_code',
        'amount',
        'payment_method',
        'status',
        'paid_at',
        'payment_reference',
        'payment_details',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'payment_details' => 'array',
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
