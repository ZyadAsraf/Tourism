<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttractionImage extends Model
{
    protected $table = 'attraction_images';
    protected $fillable = ['attraction_id', 'filename', 'alt_text'];

    public function attraction()
    {
        return $this->belongsTo(Attraction::class);
    }
}
