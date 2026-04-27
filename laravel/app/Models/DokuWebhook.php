<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokuWebhook extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'order_id',
        'transaction_id',
        'status',
        'amount',
        'signature',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'status' => 'string',
    ];

    const UPDATED_AT = null;

    protected $primaryKey = 'id';

    public function getCreatedAtColumn()
    {
        return 'received_at';
    }

    // ===== RELATIONSHIPS =====

    /**
     * The booking this webhook is for
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    // ===== HELPERS =====

    /**
     * Check if webhook status is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'PENDING';
    }

    /**
     * Check if webhook status is success
     */
    public function isSuccess(): bool
    {
        return $this->status === 'SUCCESS';
    }

    /**
     * Check if webhook status is failed
     */
    public function isFailed(): bool
    {
        return $this->status === 'FAILED';
    }
}
