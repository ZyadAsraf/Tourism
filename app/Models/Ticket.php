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
    protected $fillable = [
        'PhoneNumber',
        'QR_Code',
        'Booking_Time',
        'Quantity',
        'Visit_Date',
        'total_cost',
        'tourist_id',
        'attraction_id',
        'attraction_staff_id'
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
    public function attraction()
    {
        return $this->belongsTo(Attraction::class);
    }

    // A Ticket is managed by an Attraction Staff
    public function attractionStaff()
    {
        return $this->belongsTo(Attraction_Staff::class, 'attraction_staff_id');
    }
}
