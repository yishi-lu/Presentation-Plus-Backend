<?php

return [
    /* Auth Services */
    "App\Contracts\Auth\AuthService" => [
        "class" => "App\Services\Auth\AuthServiceImp",
        "shared" => false,
        "singleton" => true,
    ],

    "App\Contracts\Business\BusinessService" => [
        "class" => "App\Services\Business\BusinessServiceImp",
        "shared" => false,
        "singleton" => true,
    ],

    "App\Contracts\Business\ProfileService" => [
        "class" => "App\Services\Business\ProfileServiceImp",
        "shared" => false,
        "singleton" => true,
    ],

    "App\Contracts\Business\CommentService" => [
        "class" => "App\Services\Business\CommentServiceImp",
        "shared" => false,
        "singleton" => true,
    ],

    "App\Contracts\Business\MessageService" => [
        "class" => "App\Services\Business\MessageServiceImp",
        "shared" => false,
        "singleton" => true,
    ],
];
