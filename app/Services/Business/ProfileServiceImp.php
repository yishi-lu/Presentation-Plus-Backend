<?php

namespace App\Services\Business;

use App\Contracts\Business\ProfileService;

use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

use App\Contracts\Constant;
use App\User; 
use App\Post; 
use App\Profile; 

class ProfileServiceImp implements ProfileService
{

    /**
     * fetch profile by given profile id
     *
     * @param $profile_id
     * @return User|null
     */
    public function show_user_profile($profile_id){

        $profile = Profile::findOrFail($profile_id);

        $target_user = DB::table('profiles')
                    ->select('profiles.id as profile_id', 'profiles.user_id as id', 'profiles.signature', 'profiles.portrait', 'profiles.visibility', 'users.name', 'users.status')
                    ->join('users','users.id','=','profiles.user_id')
                    ->where('profiles.user_id', $profile_id)
                    ->first();

        $auth_user = Auth::guard('api')->user();

        $follows = $auth_user ? $auth_user->followed->contains($profile_id) : false;
        $followerCount = $profile->followedBy->count();
        $followingCount = $profile->user->followed->count();
        $postCount = $profile->user->post->count();

        $target_user->follow = $follows;
        $target_user->followerCount = $followerCount;
        $target_user->followingCount = $followingCount;
        $target_user->postCount = $postCount;

        $following = $auth_user ? User::find($target_user->id)->followed->contains($auth_user->profile->id) : false;
        // dd($profile->followedBy->contains($auth_user->id));

        if($auth_user && $target_user->id == $auth_user->id && $target_user->status != Constant::STATUS_DEACTIVATED) return $target_user;

        if($profile->visibility == Constant::STATUS_PRIVATE || $target_user->status == Constant::STATUS_DEACTIVATED) return null;
        else if($profile->visibility == Constant::STATUS_FOLLOWER && !$following) return null;
        else {
            return $target_user;
        }

    }

    /**
     * edit auth user profile by given profile info
     *
     * @param $profile_info
     * @return bool
     */
    public function edit_user_profile($profile_info){

        return auth()->user()->profile->update($profile_info);

    }

    /**
     * auth user follow/unfollow a profile by given profile id
     *
     * @param $profile_id
     * @return bool
     */
    public function follow_unfollow($profile_id){

        // $auth_user = Auth::user();
        $profile = Profile::findOrFail($profile_id);

        // $profile_user = DB::table('profile_user')
        // ->where('user_id', $auth_user->id)
        // ->where('profile_id', $profile_id)
        // ->get();

        // $count = $profile_user->count();

        // if($count == 0) {
        //     $profile->followedBy()->attach($auth_user->id);
        // }
        // else {
        //     $profile->followedBy()->detach($auth_user->id);
        // }

        return auth()->user()->followed()->toggle($profile);

        // return $count;
    }
}