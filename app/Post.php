<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title', 'description', 'image_url', 'content', 'type', 'status', 'visibility', 'viewed', 'liked'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class)->withTimestamps();
    }

    public function post_image()
    {
        return $this->hasMany(Post_image::class)->orderBy('order', 'ASC');
    }

    public function comment()
    {
        return $this->hasMany(Comment::class)->orderBy('order', 'DESC');
    }
}
