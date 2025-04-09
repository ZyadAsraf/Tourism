<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Governorate extends Model
{
    use HasFactory;
    protected $table = 'governorates'; // Ensures it matches the table name

    protected $primaryKey = 'id'; 
    public $incrementing = true;

    protected $fillable = [
        'Name',
    ];

    /**
     * Define Relationships
     */

    // A Governorate can have multiple Attractions
    public function attractions()
    {
        return $this->hasMany(Attraction::class);
    }
    public static function canViewAny(): bool
    {
        return true; // Temporary override
    }
}
