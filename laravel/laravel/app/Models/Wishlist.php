<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wishlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'villa_id',
    ];

    public $timestamps = false;

    // ===== RELATIONSHIPS =====

    /**
     * The user who added to wishlist
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The villa in the wishlist
     */
    public function villa(): BelongsTo
    {
        return $this->belongsTo(Villa::class);
    }
}
