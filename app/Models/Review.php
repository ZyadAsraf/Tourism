<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = 'reviews';
    protected $fillable = [
        'rating',
        'comment',
        'tourist_id',
        'attraction_id',
    ];
    public function tourist()
    {
        return $this->belongsTo(User::class, 'tourist_id');
    }
    public function attraction()
    {
        return $this->belongsTo(Attraction::class, 'attraction_id');
    }
}
