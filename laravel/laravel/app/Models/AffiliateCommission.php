<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateCommission extends Model
{
    use HasFactory;

    protected $fillable = [
        'affiliate_user_id',
        'booking_id',
        'commission_amount',
        'status',
        'eligible_at',
    ];

    protected $casts = [
        'commission_amount' => 'decimal:2',
        'status' => 'string',
        'eligible_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    const UPDATED_AT = null;

    // ===== RELATIONSHIPS =====

    /**
     * The affiliate user earning this commission
     */
    public function affiliateUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'affiliate_user_id');
    }

    /**
     * The booking this commission is for
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    // ===== HELPERS =====

    /**
     * Check if commission is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if commission is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if commission is paid
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Check if commission is eligible (past eligible_at date)
     */
    public function isEligible(): bool
    {
        if (is_null($this->eligible_at)) {
            return true;
        }
        return now()->isAfter($this->eligible_at);
    }

    /**
     * Mark commission as approved
     */
    public function markAsApproved(): void
    {
        $this->update(['status' => 'approved']);
    }

    /**
     * Mark commission as paid
     */
    public function markAsPaid(): void
    {
        $this->update(['status' => 'paid']);
    }
}
