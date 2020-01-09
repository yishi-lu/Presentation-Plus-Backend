<?php

namespace App\Contracts\Business;

/**
 * Profile Service interface
 *
 * Created by Yishi Lu.
 * User: Yishi Lu
 * Date: 2019/12/28
 */

interface ProfileService
{
    //fetch profile by given profile id
    public function show_user_profile($profile_id);

    //edit auth user profile by given profile info
    public function edit_user_profile($profile_info);

    //auth user follow/unfollow a profile by given profile id
    public function follow_unfollow($profile_id);

}