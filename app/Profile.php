<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'signature', 'portrait', 'visibility'
    ];

    public function user(Type $var = null)
    {
        return $this->belongsTo(User::class);
    }

    public function followedBy(){
        
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
