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
        'QRCode',
        'BookingTime',
        'Quantity',
        'VisitDate',
        'TotalCost',
        'TouristId',
        'AttractionId',
        'AttractionStaffId'
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
        return $this->belongsTo(User::class, 'AttractionStaffId');
    }
}
