<?php

namespace App\Contracts;

/**
 * Created by Yishi Lu.
 * User: Yishi Lu
 */
interface Constant
{
    // Role
    const ROLE_SUPER_ADMIN = 99;
    const ROLE_ADMIN = 1;
    const ROLE_USER = 0;

    // User status
    const STATUS_REGISTER_PROCESSING = 2;
    const STATUS_USER_ACTIVATED = 1;
    const STATUS_USER_DEACTIVATED = 0;

    // User Gender
    const GENDER_SECRET = 0;
    const GENDER_FEMALE = 1;
    const GENDER_MALE = 2;
    const GENDER_SECRET_STR = "secret";
    const GENDER_FEMALE_STR = "female";
    const GENDER_MALE_STR = "male";

    // Subscription status
    const STATUS_SUBSCRIBED = 1;
    const STATUS_UNSUBSCRIBED = 0;

    // Permission
    const READABLE = 1;
    const WRITABLE = 2;

    /** Common Status **/
    //Activated or Deactivated
    const STATUS_ACTIVATED = 1;
    const STATUS_DEACTIVATED = 0;

    // Public or Private;
    const STATUS_FOLLOWER = 2;
    const STATUS_PUBLIC = 1;
    const STATUS_PRIVATE = 0;

    //Post ordering
    const ORDER_BY_DATE = "creation_time";
    const ORDER_BY_LIKE = "like";
    const ORDER_BY_VIEW = "viwed";
    const ORDER_BY_COMMENT = "num_comment";

    //Post type 
    const POST_TYPE_NORMAL = 1;
    const POST_TYPE_SLIDES = 2;
    const POST_TYPE_VIDEO = 3;
    const POST_TYPE_COMIC = 4;

}