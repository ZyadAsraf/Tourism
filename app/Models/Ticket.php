<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'tickets'; // Ensures it matches the table name

    // protected $primaryKey = 'id'; // UUID primary key
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'PhoneNumber',
        'BookingTime',
        'Quantity',
        'TotalCost',
        'Attraction',
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


public function attraction()
{
    return $this->belongsTo(Attraction::class, 'Attraction'); // or the actual foreign key column
}


    // A Ticket is managed by an Attraction Staff
    public function attractionStaff()
    {
        return $this->belongsTo(User::class, 'AttractionStaffId');
    }
public function ticketType()
{
    return $this->belongsTo(TicketType::class, 'TicketTypesId');
}

    protected static function booted()
    {
        static::creating(function ($model) {
            // Only set if not already provided
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }
        });
    }
}

