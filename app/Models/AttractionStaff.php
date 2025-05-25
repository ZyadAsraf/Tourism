<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttractionStaff extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'attraction_staff';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'attraction_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'user_id' => 'string', // UUID cast
    ];

    /**
     * Get the user that belongs to this attraction staff.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the attraction that this staff belongs to.
     */
    public function attraction(): BelongsTo
    {
        return $this->belongsTo(Attraction::class);
    }
    public static function assignUserToAttractions($userId, $attractionIds)
{
    // First, remove existing assignments for this user if needed
    // static::where('user_id', $userId)->delete();
    
    // Create new assignments
    foreach ($attractionIds as $attractionId) {
        static::firstOrCreate([
            'user_id' => $userId,
            'attraction_id' => $attractionId,
        ]);
    }
}
}