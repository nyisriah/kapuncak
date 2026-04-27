<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingDate extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'villa_id',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public $timestamps = false;

    // ===== RELATIONSHIPS =====

    /**
     * The booking this date belongs to
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * The villa for this date
     */
    public function villa(): BelongsTo
    {
        return $this->belongsTo(Villa::class);
    }
}
