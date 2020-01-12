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
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function commentedOn()
    {
        return $this->belongsTo(Comment::class);
    }

    public function haveComment()
    {
        return $this->hasMany(Comment::class);
    }

    public function thumbedBy(){
        
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
