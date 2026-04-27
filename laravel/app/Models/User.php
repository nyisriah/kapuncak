<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Mail\VerifyEmailMail;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'whatsapp_number',
        'address',
        'role',
        'affiliate_code',
        'referral_code',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'role' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ===== RELATIONSHIPS =====

    /**
     * Bookings created by this user
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Bookings where this user is the affiliate
     */
    public function affiliateBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'affiliate_user_id');
    }

    /**
     * Reviews written by this user
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Refresh tokens for authentication
     */
    public function refreshTokens(): HasMany
    {
        return $this->hasMany(RefreshToken::class);
    }

    /**
     * Villas in wishlist (many-to-many through wishlist table)
     */
    public function wishlistVillas(): BelongsToMany
    {
        return $this->belongsToMany(Villa::class, 'wishlist', 'user_id', 'villa_id');
    }

    /**
     * Affiliate commissions earned
     */
    public function affiliateCommissions(): HasMany
    {
        return $this->hasMany(AffiliateCommission::class, 'affiliate_user_id');
    }

    /**
     * Audit logs related to this user
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Users that were referred by this user (self-referential)
     */
    public function referrals(): HasMany
    {
        return $this->hasMany(User::class, 'referral_code', 'affiliate_code');
    }

    /**
     * The user who referred this user
     */
    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referral_code', 'affiliate_code');
    }

    // ===== HELPERS =====

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is regular user
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Check if user can access admin panel
     */
    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return match ($panel->getId()) {
            'admin' => $this->role === 'admin',
            'user' => $this->role === 'user',
            default => false,
        };
    }

    /**
     * Check if user has affiliate code
     */
    public function isAffiliate(): bool
    {
        return !is_null($this->affiliate_code);
    }

    /**
     * Get total commission earned
     */
    public function getTotalCommissionEarned(): float
    {
        return (float) $this->affiliateCommissions()
            ->where('status', 'paid')
            ->sum('commission_amount');
    }

    /**
     * Get pending commission
     */
    public function getPendingCommission(): float
    {
        return (float) $this->affiliateCommissions()
            ->where('status', 'pending')
            ->sum('commission_amount');
    }

    /**
     * Get total bookings completed
     */
    public function getCompletedBookingsCount(): int
    {
        return (int) $this->bookings()
            ->where('status', 'completed')
            ->count();
    }

    /**
     * Get total revenue from bookings
     */
    public function getTotalRevenue(): float
    {
        return (float) $this->bookings()
            ->whereIn('status', ['paid', 'completed'])
            ->sum('total_price');
    }

    /**
     * Get active bookings (not completed or cancelled)
     */
    public function getActiveBookings()
    {
        return $this->bookings()
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->get();
    }

    /**
     * Send email verification notification
     */
    public function sendEmailVerificationNotification()
{
    $this->notify(new \Illuminate\Auth\Notifications\VerifyEmail);
}
    /**
     * Send password reset notification
     */
    public function sendPasswordResetNotification($token)
{
    $this->notify(new \Illuminate\Auth\Notifications\ResetPassword($token));
}
}

