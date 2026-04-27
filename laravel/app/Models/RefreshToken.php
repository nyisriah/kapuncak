<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RefreshToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token_hash',
        'expires_at',
        'revoked',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'revoked' => 'boolean',
        'created_at' => 'datetime',
    ];

    const UPDATED_AT = null;

    // ===== RELATIONSHIPS =====

    /**
     * The user this token belongs to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ===== HELPERS =====

    /**
     * Check if token is expired
     */
    public function isExpired(): bool
    {
        return now()->isAfter($this->expires_at);
    }

    /**
     * Check if token is still valid (not expired and not revoked)
     */
    public function isValid(): bool
    {
        return !$this->isExpired() && !$this->revoked;
    }

    /**
     * Revoke this token
     */
    public function revoke(): void
    {
        $this->update(['revoked' => true]);
    }
}
