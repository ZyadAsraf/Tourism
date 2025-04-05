<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        "ArticleLinks",
        "ArticleHeading",
        'ArticleBody',
        'Img',
        'AdminId',
    ] ;
    
    public function admin(){
        return $this->belongsTo(NormalAdmin::class);
    }
}
