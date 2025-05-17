<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        "ArticleHeading",
        'ArticleBody',
        'Img',
        'AdminId',
    ] ;
    
    public function admin(){
        return $this->belongsTo(User::class);
    }
    public function attractions() {
        return $this->belongsToMany(Attraction::class, 'article_links', 'article_id', 'attraction_id');
    }
}
