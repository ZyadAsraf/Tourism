<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail, HasAvatar, HasName, HasMedia
{
    use InteractsWithMedia;
    use HasUuids, HasRoles;
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'firstname',
        'lastname',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getFilamentName(): string
    {
        return $this->username;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // if ($panel->getId() === 'admin') {
        //     return str_ends_with($this->email, '@yourdomain.com') && $this->hasVerifiedEmail();
        // }

        return true;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        $media = $this->getFirstMedia('avatars');
    
        return $media?->getUrl('thumb') ?? $media?->getUrl() ?? null;
    }

    // Define an accessor for the 'name' attribute
    public function getNameAttribute()
    {
        return "{$this->firstname} {$this->lastname}";
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(config('filament-shield.super_admin.name'));
    }

    public function registerMediaConversions(Media|null $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Contain, 300, 300)
            ->nonQueued();
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    public function attractionStaff(): HasMany
    {
        return $this->hasMany(AttractionStaff::class);
    }

    /**
     * Check if the user can verify tickets for a specific attraction.
     * Super admins can verify any attraction tickets.
     * Attraction staff can only verify tickets for attractions they are assigned to.
     *
     * @param int $attractionId
     * @return bool
     */
    public function canVerifyAttractionTickets($attractionId): bool
    {
        // Super admins can verify any attraction tickets
        if ($this->hasRole('super_admin')) {
            return true;
        }

        // Attraction staff can only verify tickets for attractions they are assigned to
        if ($this->hasRole('attraction_staff')) {
            return $this->attractionStaff()
                ->where('attraction_id', $attractionId)
                ->exists();
        }

        // Users without appropriate roles cannot verify tickets
        return false;
    }

    /**
     * Get all attractions this user staffs (many-to-many through pivot).
     */
    public function staffedAttractions(): BelongsToMany
    {
        return $this->belongsToMany(Attraction::class, 'attraction_staff');
    }
}
