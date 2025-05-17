<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class articleLinks extends Model
{
    protected $table = 'article_links';
    protected $fillable = ['attraction_id', 'article_id',];

    public function attraction()
    {
        return $this->belongsTo(Attraction::class);
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
