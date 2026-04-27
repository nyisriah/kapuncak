<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'amount',
        'proof',
        'payment_method',
        'doku_transaction_id',
        'status',
        'webhook_data',
        'rejection_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'status' => 'string',
        'webhook_data' => 'array',
        'created_at' => 'datetime',
        'rejection_reason' => 'string',
    ];

    const UPDATED_AT = null;

    // ===== RELATIONSHIPS =====

    /**
     * The booking this payment is for
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    // ===== HELPERS =====

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if payment is successful
     */
    public function isSuccess(): bool
    {
        return $this->status === 'success';
    }

    /**
     * Check if payment failed
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Mark payment as successful
     */
    public function markAsSuccess(): void
    {
        $this->update(['status' => 'success']);
    }

    /**
     * Mark payment as failed
     */
    public function markAsFailed(): void
    {
        $this->update(['status' => 'failed']);
    }

    /**
     * Check if payment is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Approve payment and update booking status
     */
    public function approve(?string $note = null): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $updated = $this->update(['status' => 'success']);
        
        if ($updated && $this->booking) {
            $this->booking->confirm();
        }

        return $updated;
    }

    /**
     * Reject payment with reason
     */
    public function reject(?string $reason = null): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $updated = $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);

        if ($updated && $this->booking) {
            // Revert booking status to approved so user can re-upload
            $this->booking->update(['status' => 'approved']);
        }

        return $updated;
    }

    /**
     * Reset payment to pending for re-upload
     */
    public function resetForReupload(): bool
    {
        return $this->update([
            'status' => 'pending',
            'rejection_reason' => null,
            'proof' => null, // Will be updated with new proof
        ]);
    }

    /**
     * Get status badge color for UI
     */
    public function getStatusColor(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'success' => 'success',
            'rejected' => 'danger',
            'failed' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get formatted status for display
     */
    public function getFormattedStatus(): string
    {
        return match ($this->status) {
            'pending' => 'Pending Verification',
            'success' => 'Payment Confirmed',
            'rejected' => 'Payment Rejected',
            'failed' => 'Payment Failed',
            default => ucfirst($this->status),
        };
    }
}
