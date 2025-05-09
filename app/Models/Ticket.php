<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'tickets'; // Ensures it matches the table name

    protected $primaryKey = 'id'; // UUID primary key
    public $incrementing = true;
    protected $keyType = 'string';
    
    protected $fillable = [
        'PhoneNumber',
        'BookingTime',
        'Quantity',
        'TotalCost',
        'state',
        'TicketTypesId',
        'AttractionStaffId',
        'VisitDate',
        'TouristId',
    ];
    
    

    /**
     * Define Relationships
     */

    // A Ticket belongs to a Tourist
    public function tourist()
    {
        return $this->belongsTo(User::class);
    }

    // A Ticket belongs to an Attraction
// Ticket.php
public function attractions()
{
    return $this->belongsToMany(Attraction::class)->withPivot('quantity', 'visit_date');
}


    // A Ticket is managed by an Attraction Staff
    public function attractionStaff()
    {
        return $this->belongsTo(User::class, 'AttractionStaffId');
    }
    public function ticketType()
{
    return $this->belongsTo(TicketType::class, 'TicketTypeId');
}

}

