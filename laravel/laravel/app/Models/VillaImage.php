<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VillaImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'villa_id',
        'image_path',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    const UPDATED_AT = null;

    // ===== RELATIONSHIPS =====

    /**
     * The villa this image belongs to
     */
    public function villa(): BelongsTo
    {
        return $this->belongsTo(Villa::class);
    }
}
