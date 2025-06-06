<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AttractionCategory extends Pivot
{
    use HasFactory;

    protected $table = 'attraction_category'; // Set the pivot table name
    public $incrementing = true; 
    public $timestamps = false; // Pivot tables usually don’t have timestamps

    protected $fillable = [
        'AttractionId',
        'CategoryId'
    ];
}
