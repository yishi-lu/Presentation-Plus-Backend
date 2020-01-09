<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    //

    protected $fillable = [
        'title', 'content', 'liked', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTimestamps();
    }

    public function post()
    {
        return $this->belongsTo(Post::class)->withTimestamps();
    }

    public function commentedOn()
    {
        return $this->belongsTo(Comment::class)->withTimestamps();
    }

    public function haveComment()
    {
        return $this->hasMany(Comment::class)->withTimestamps();
    }
}
