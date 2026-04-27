<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvalidatedToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'jti',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public $timestamps = false;

    protected $primaryKey = 'jti';
    protected $keyType = 'string';
    public $incrementing = false;

    // ===== HELPERS =====

    /**
     * Check if token is expired
     */
    public function isExpired(): bool
    {
        return now()->isAfter($this->expires_at);
    }

    /**
     * Clean up expired tokens
     */
    public static function cleanupExpired(): int
    {
        return self::where('expires_at', '<', now())->delete();
    }
}
