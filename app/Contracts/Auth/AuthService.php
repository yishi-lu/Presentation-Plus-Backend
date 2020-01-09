<?php

namespace App\Contracts\Auth;

/**
 * Auth Service interface
 *
 * Created by Yishi Lu.
 * User: Yishi Lu
 * Date: 2019/12/39
 */

interface AuthService
{
    //login user by email and password
    public function login($email, $password, $remember = true);

    //register user
    public function register($userInfo);

}