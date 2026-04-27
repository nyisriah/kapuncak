<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'villa_id',
        'booking_id',
        'rating',
        'comment',
    ];

    protected $casts = [
        'rating' => 'integer',
        'created_at' => 'datetime',
    ];

    const UPDATED_AT = null;

    // ===== RELATIONSHIPS =====

    /**
     * The user who wrote this review
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The villa being reviewed
     */
    public function villa(): BelongsTo
    {
        return $this->belongsTo(Villa::class);
    }

    /**
     * The booking this review is for
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    // ===== HELPERS =====

    /**
     * Check if rating is positive (4-5 stars)
     */
    public function isPositive(): bool
    {
        return $this->rating >= 4;
    }

    /**
     * Check if rating is neutral (3 stars)
     */
    public function isNeutral(): bool
    {
        return $this->rating === 3;
    }

    /**
     * Check if rating is negative (1-2 stars)
     */
    public function isNegative(): bool
    {
        return $this->rating <= 2;
    }

    /**
     * Get rating as percentage (e.g., 4 stars = 80%)
     */
    public function getRatingPercentage(): float
    {
        return ($this->rating / 5) * 100;
    }
}
