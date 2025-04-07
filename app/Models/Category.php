<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories'; // Ensures it matches the table name
    protected $primaryKey = 'id'; 
    public $incrementing = true; 
    protected $fillable = [
        'Name',
        'Description',
        'Img',
    ];

    /**
     * Define Relationships
     */

    // A Category can have multiple Attractions
    public function attractions()
    {
        return $this->belongsToMany(Attraction::class, 'attraction_category', 'CategoryId', 'AttractionId')->withTimestamps();
    }
    
}
