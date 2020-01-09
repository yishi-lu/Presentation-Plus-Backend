<?php

namespace App\Services\Auth;

use App\Contracts\Auth\AuthService;
use App\User; 

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthServiceImp implements AuthService
{

    public $successStatus = 200;


    /**
     * Login user by email and password
     *
     * @param $email
     * @param null $password
     * @param bool $remember
     * @return User|null
     */
    public function login($given_email, $given_password, $remember = true){

        $email = $given_email;
        $password = $given_password;

        $user = User::where("email", $email)->first();
        if ($user==null || !Hash::check($password, $user->password)) $user = null;

        return $user;
    }

    /**
     * register user
     *
     * @param $userInfo
     * @return User|null
     */
    public function register($userInfo){

        $input = $userInfo->all();  
        $input['password'] = Hash::make($userInfo['password']);
        // $input['portrait'] = "https://i.picsum.photos/id/38/300/300.jpg";

        $user = User::create($input); 

        return $user;

    }

}