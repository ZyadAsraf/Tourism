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
        'AttractionName',
        'Description',
        'Address',
        'City',
        'Street',
        'LocationLink',
        'Img',
        'EntryFee',
        'AdminId',
        'Status',
        'GovernorateId',
        'TicketTypesId' 
    ];

    /**
     * Define Relationships
     */

    // Relationship with Admin
    public function admin()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with Governorate
    public function governorate()
    {
        return $this->belongsTo(Governorate::class, 'id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'attraction_category', 'AttractionId', 'CategoryId')->withTimestamps();
    }
    



    public function ticketType()
    {
        return $this->belongsTo(TicketType::class, 'TicketTypesId');
    }
}
