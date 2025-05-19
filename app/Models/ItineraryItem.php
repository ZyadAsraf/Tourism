<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItineraryItem extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'uuid';
    protected $primaryKey = 'uuid';

    protected $fillable = [
        'uuid', 'itinerary_id', 'attraction_id',
        'date', 'time', 'quantity',
        'position', 'TicketTypeId'
    ];

    protected $casts = [
        'quantity' => 'integer'
    ];

    public function itinerary()
    {
        return $this->belongsTo(Itinerary::class);
    }

    public function attraction()
    {
        return $this->belongsTo(Attraction::class);
    }

    public function ticketType()
    {
        return $this->belongsTo(TicketType::class, 'TicketTypeId');
    }
}
