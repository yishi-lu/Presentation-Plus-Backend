<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'phone', 'status', 'role',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    //create user profile after user is registered
    protected static function boot(){
    
        parent::boot();

        static::created(function ($user){
            $user->profile()->create([
                'portrait' => 'https://i.picsum.photos/id/518/300/300.jpg',
                'signature' => 'The person is lazy and does not write anything...',
                'visibility' => \App\Contracts\Constant::STATUS_PUBLIC,
            ]);
        });
    }

    public function post(Type $var = null)
    {
        return $this->hasMany(Post::class)->orderBy('created_at', 'DESC');
    }

    public function profile(Type $var = null)
    {
        return $this->hasOne(Profile::class);
    }

    public function followed(){
        return $this->belongsToMany(Profile::class)->withTimestamps();;
    }

    public function comment(Type $var = null)
    {
        return $this->hasMany(Comment::class)->orderBy('created_at', 'DESC');
    }
    
    public function thumbed(){
        return $this->belongsToMany(Comment::class)->withTimestamps();;
    }

    public function collected(){
        return $this->belongsToMany(Post::class)->withTimestamps();
    }
}
