<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Itinerary extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'uuid';
    protected $primaryKey = 'uuid';

    protected $fillable = [
        'uuid', 'user_id', 'type_id', 'name',
        'description', 'public', 'likes'
    ];

    protected $casts = [
        'public' => 'boolean',
        'likes' => 'integer',
    ];

    public function type()
    {
        return $this->belongsTo(ItineraryType::class, 'type_id');
    }

    public function items()
    {
        return $this->hasMany(ItineraryItem::class, 'itinerary_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
