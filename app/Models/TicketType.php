<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketType extends Model
{
    use HasFactory;

    protected $table = 'ticket_types'; // Matches table name in migration

    protected $primaryKey = 'id'; 
    public $incrementing = true;
    protected $fillable = [
        'Title',
        'Description',
        'DiscountAmount',
    ];

    /**
     * Define Relationships
     */

    // A Ticket Type can be linked to multiple Attractions or Tickets
    public function attractions()
    {
        return $this->hasMany(Attraction::class);
    }
}
