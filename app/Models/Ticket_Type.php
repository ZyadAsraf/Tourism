<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket_Type extends Model
{
    use HasFactory;

    protected $table = 'ticket__types'; // Matches table name in migration

    protected $primaryKey = 'id'; 
    public $incrementing = true;
    protected $fillable = [
        'title',
        'description',
        'Discount_Amount',
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
