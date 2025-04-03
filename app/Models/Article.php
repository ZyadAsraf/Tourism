<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        "article_links",
        "article_heading",
        'article_body',
        'img',
        'admin_id',
    ] ;
    public function admin(){
        return $this->belongsTo(Admin::class);
    }
}
