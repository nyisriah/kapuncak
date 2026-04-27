<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'villa_id',
        'checkin_date',
        'checkout_date',
        'guest',
        'status',
        'user_ip',
        'total_price',
        'markup_amount',
        'affiliate_user_id',
        'approved_at',
        'payment_deadline',
    ];

    protected $casts = [
        'checkin_date' => 'date',
        'checkout_date' => 'date',
        'status' => 'string',
        'total_price' => 'decimal:2',
        'markup_amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'payment_deadline' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ===== RELATIONSHIPS =====

    /**
     * The user who made the booking
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The villa being booked
     */
    public function villa(): BelongsTo
    {
        return $this->belongsTo(Villa::class);
    }

    /**
     * The affiliate user who referred this booking (nullable)
     */
    public function affiliateUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'affiliate_user_id');
    }

    /**
     * Individual booking dates
     */
    public function bookingDates(): HasMany
    {
        return $this->hasMany(BookingDate::class);
    }

    /**
     * Payment for this booking
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * DOKU webhook for this booking
     */
    public function dokuWebhook(): HasOne
    {
        return $this->hasOne(DokuWebhook::class);
    }

    /**
     * Affiliate commission for this booking
     */
    public function affiliateCommission(): HasOne
    {
        return $this->hasOne(AffiliateCommission::class);
    }

    /**
     * Reviews for this booking
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // ===== AUTHORIZATION METHODS =====

    /**
     * Check if user can edit this booking
     */
    public function canBeEditedBy(User $user): bool
    {
        // Only owner, and only if not confirmed
        return $this->user_id === $user->id && !in_array($this->status, ['confirmed', 'rejected', 'paid']);
    }

    /**
     * Check if user can view this booking
     */
    public function canBeViewedBy(User $user): bool
    {
        // Only owner or admin
        return $this->user_id === $user->id || $user->isAdmin();
    }

    /**
     * Check if status transition is valid
     * @param string $newStatus
     * @return bool
     */
    public function canTransitionTo(string $newStatus): bool
    {
        $validTransitions = [
            'pending' => ['approved', 'rejected'],
            'approved' => ['paid'],
            'paid' => ['confirmed', 'rejected'],
            'confirmed' => [],
            'rejected' => [],
        ];

        return in_array($newStatus, $validTransitions[$this->status] ?? []);
    }

    /**
     * Get number of nights
     */
    public function getNights(): int
    {
        return (int) $this->checkin_date->diffInDays($this->checkout_date);
    }

    /**
     * Check if booking is pending payment verification
     */
    public function isPendingPayment(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Check if booking is paid
     */
    public function isPaid(): bool
    {
        return in_array($this->status, ['paid', 'confirmed']);
    }

    /**
     * Check if booking is confirmed
     */
    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    /**
     * Check if booking is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if booking has passed (checkout is in the past)
     */
    public function hasPassed(): bool
    {
        return now()->isAfter($this->checkout_date);
    }

    /**
     * Check if user can review (booking is completed and not yet reviewed)
     */
    public function canBeReviewed(): bool
    {
        return $this->isCompleted() && !$this->reviews()->exists();
    }

    /**
     * Get total amount including markup and commission
     */
    public function getTotalWithMarkup(): float
    {
        return (float) $this->total_price + (float) ($this->markup_amount ?? 0);
    }

    /**
     * Get net price (before affiliate commission)
     */
    public function getNetPrice(): float
    {
        $affiliate = $this->affiliateCommission;
        if ($affiliate) {
            return (float) ($this->total_price - $affiliate->commission_amount);
        }
        return (float) $this->total_price;
    }

    // ===== ACCESSORS =====

    public function getCheckInAttribute()
    {
        return $this->checkin_date;
    }

    public function getCheckOutAttribute()
    {
        return $this->checkout_date;
    }

    // ===== ENHANCED BOOKING FLOW METHODS =====

    /**
     * Check if booking is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if booking is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Check if booking has expired (past payment deadline)
     */
    public function isExpired(): bool
    {
        if (!$this->payment_deadline) {
            return false;
        }
        return now()->isAfter($this->payment_deadline);
    }

    /**
     * Check if user can access payment page
     */
    public function canAccessPayment(): bool
    {
        return $this->isApproved() && !$this->isExpired();
    }

    /**
     * Approve booking and set payment deadline
     */
    public function approve(int $hours = 24): bool
    {
        if (!$this->canTransitionTo('approved')) {
            return false;
        }

        return $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'payment_deadline' => now()->addHours($hours),
        ]);
    }

    /**
     * Cancel booking (for expired or manual cancellation)
     */
    public function cancel(): bool
    {
        return $this->update(['status' => 'cancelled']);
    }

    /**
     * Get remaining time until payment deadline
     */
    public function getRemainingTime(): ?string
    {
        if (!$this->payment_deadline) {
            return null;
        }

        $remaining = now()->diff($this->payment_deadline);
        
        if ($remaining->invert) {
            return 'Expired';
        }

        if ($remaining->days > 0) {
            return $remaining->days . ' day' . ($remaining->days > 1 ? 's' : '') . ' ' . $remaining->h . ' hours';
        }

        return $remaining->h . ' hour' . ($remaining->h > 1 ? 's' : '') . ' ' . $remaining->i . ' minutes';
    }

    /**
     * Check if payment can be uploaded
     */
    public function canUploadPayment(): bool
    {
        return $this->isApproved() && !$this->isExpired() && !$this->payment;
    }

    /**
     * Check if payment can be re-uploaded (after rejection)
     */
    public function canReuploadPayment(): bool
    {
        return $this->isApproved() && !$this->isExpired() && 
               $this->payment && $this->payment->isRejected();
    }

    /**
     * Mark as confirmed after successful payment
     */
    public function confirm(): bool
    {
        if (!$this->canTransitionTo('confirmed')) {
            return false;
        }

        return $this->update(['status' => 'confirmed']);
    }

    /**
     * Update status transitions for new flow
     */
    public function canTransitionTo(string $newStatus): bool
    {
        $validTransitions = [
            'pending' => ['approved', 'rejected', 'cancelled'],
            'approved' => ['cancelled', 'paid'],
            'paid' => ['confirmed', 'approved', 'cancelled'],
            'confirmed' => [],
            'rejected' => [],
            'cancelled' => [],
        ];

        return in_array($newStatus, $validTransitions[$this->status] ?? []);
    }
}
