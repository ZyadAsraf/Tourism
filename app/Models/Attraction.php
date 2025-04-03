<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attraction extends Model
{
    use HasFactory;

    protected $table = 'attractions'; // Ensure it matches the actual table name
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'Attraction_Name',
        'Description',
        'address',
        'city',
        'street',
        'location_link',
        'img',
        'entryFee',
        'admin_id',
        'status',
        'governorate_id',
        'ticket_types_id' 
    ];

    /**
     * Define Relationships
     */

    // Relationship with Admin
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    // Relationship with Governorate
    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'attraction_category')->withTimestamps();
    }

    public function ticketType()
    {
        return $this->belongsTo(Ticket_Type::class, 'ticket_types_id');
    }
}
