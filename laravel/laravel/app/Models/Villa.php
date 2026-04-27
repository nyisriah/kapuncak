<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Villa extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'location',
        'max_guests',
        'bedrooms',
        'weekday_price',
        'weekend_price',
        'facilities',
        'rules',
        'images',
        'status',
        'contact_pengelola_villa',
        'google_maps_link',
    ];

    protected $casts = [
        'max_guests' => 'integer',
        'bedrooms' => 'integer',
        'weekday_price' => 'decimal:2',
        'weekend_price' => 'decimal:2',
        'facilities' => 'array',
        'images' => 'array',
        'status' => 'string',
        'created_at' => 'datetime',
    ];

    const UPDATED_AT = null;

    protected static function booted()
    {
        static::creating(function (Villa $villa) {
            if (empty($villa->slug)) {
                $villa->slug = static::generateUniqueSlug($villa->name);
            } else {
                $villa->slug = static::generateUniqueSlug($villa->slug, null);
            }
        });

        static::updating(function (Villa $villa) {
            if (empty($villa->slug)) {
                $villa->slug = static::generateUniqueSlug($villa->name, $villa->id);
            } elseif ($villa->isDirty('slug')) {
                $villa->slug = static::generateUniqueSlug($villa->slug, $villa->id);
            }
        });
    }

    public static function generateUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($value ?: Str::random(8));
        if (empty($baseSlug)) {
            $baseSlug = Str::random(8);
        }

        $slug = $baseSlug;
        $suffix = 1;

        while (static::where('slug', $slug)
            ->when($ignoreId, fn ($query, $ignoreId) => $query->where('id', '<>', $ignoreId))
            ->exists()) {
            $slug = $baseSlug . '-' . $suffix++;
        }

        return $slug;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // ===== RELATIONSHIPS =====

    /**
     * Images for this villa
     */
    
    /**
     * Bookings for this villa
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Reviews for this villa
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Users who have this villa in their wishlist
     */
    public function wishlistUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'wishlist', 'villa_id', 'user_id');
    }

    /**
     * Booking dates (anti double booking)
     */
    public function bookingDates(): HasMany
    {
        return $this->hasMany(BookingDate::class);
    }

    // ===== HELPERS =====

    /**
     * Check if villa is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get average rating from reviews
     */
    public function getAverageRating(): float
    {
        $average = $this->reviews()->average('rating');
        return $average ?? 0;
    }

    /**
     * Get number of reviews
     */
    public function getReviewCount(): int
    {
        return (int) $this->reviews()->count();
    }

    /**
     * Check if villa is available for dates
     */
    public function isAvailableForDates($checkinDate, $checkoutDate): bool
    {
        $bookedDates = $this->bookingDates()
            ->whereBetween('date', [$checkinDate, $checkoutDate])
            ->exists();

        return !$bookedDates;
    }

    /**
     * Get price for a specific date (weekday vs weekend)
     */
    public function getPriceForDate($date): float
    {
        $day = date('N', strtotime($date)); // 1-7 (Mon-Sun)
        // Assuming weekends are Saturday (6) and Sunday (7)
        return in_array($day, [5, 6]) ? $this->weekend_price : $this->weekday_price;
    }

    /**
     * Get total number of wishlist adds
     */
    public function getWishlistCount(): int
    {
        return (int) $this->wishlistUsers()->count();
    }

    /**
     * Get completed bookings count
     */
    public function getCompletedBookingsCount(): int
    {
        return (int) $this->bookings()
            ->where('status', 'completed')
            ->count();
    }

    /**
     * Get total revenue
     */
    public function getTotalRevenue(): float
    {
        return (float) $this->bookings()
            ->whereIn('status', ['paid', 'completed'])
            ->sum('total_price');
    }
}