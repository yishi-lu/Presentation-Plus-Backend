<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post_image extends Model
{
    //

    protected $table = 'post_images';

    protected $fillable = [
        'content_image', 'order'
    ];

    public function post(Type $var = null)
    {
        return $this->belongsTo(Post::class);
    }
}
