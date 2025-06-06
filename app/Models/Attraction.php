<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Attraction extends Model
{
    use HasFactory;

    protected $table = 'attractions'; // Ensure it matches the actual table name
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'AttractionName',
        'Description',
        'Address',
        'City',
        'Street',
        'LocationLink',
        'Img',
        'EntryFee',
        'AdminId',
        'Status',
        'GovernorateId',
        'TicketTypesId',
        'expected_duration'
    ];

    /**
     * Define Relationships
     */

    // Relationship with Admin

    public function admin()
    {
        return $this->belongsTo(User::class,'AdminId');
    }

    // Relationship with Governorate
    public function governorate()
    {
        return $this->belongsTo(Governorate::class,'GovernorateId');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'attraction_category', 'AttractionId', 'CategoryId')->withTimestamps();
    }
    
    public function ticketType()
    {
        return $this->belongsTo(TicketType::class, "id");
    }

    public function reviews()
    {
    return $this->hasMany(Review::class,'attraction_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'Attraction');
    }

    public function articles(){
        return $this->belongsToMany(Article::class, 'article_links', 'attraction_id', 'article_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($attraction) {
            // Delete related reviews before deleting the attraction
            $attraction->reviews()->delete();
        });
    }
    public function images()
    {
        return $this->hasMany(AttractionImage::class);
    }

    public function images360()
    {
        return $this->hasMany(Attraction360Image::class);
    }
    /**
     * Get all attraction staff records for this attraction.
     */
    public function attractionStaff(): HasMany
    {
        return $this->hasMany(AttractionStaff::class);
    }

    /**
     * Get all users who staff this attraction (many-to-many through pivot).
     */
    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'attraction_staff');
    }
}
