<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
        'created_at' => 'datetime',
    ];

    const UPDATED_AT = null;

    // ===== RELATIONSHIPS =====

    /**
     * The user who performed the action (nullable for system actions)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ===== HELPERS =====

    /**
     * Get action description
     */
    public function getActionDescription(): string
    {
        $actions = [
            'booking_created' => 'Booking Created',
            'booking_cancelled' => 'Booking Cancelled',
            'payment_received' => 'Payment Received',
            'review_created' => 'Review Posted',
            'user_updated' => 'Profile Updated',
            'commission_approved' => 'Commission Approved',
        ];

        return $actions[$this->action] ?? ucwords(str_replace('_', ' ', $this->action));
    }

    /**
     * Get user name or "System" if no user
     */
    public function getActorName(): string
    {
        return $this->user?->name ?? 'System';
    }
}
