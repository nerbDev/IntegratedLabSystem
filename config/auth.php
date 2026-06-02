<?php

use App\Models\UserAccount;

return [

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'useraccounts',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users', // must match provider key
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\UserAccount::class, // your custom model
        ],
    ],

    'passwords' => [
        'useraccounts' => [
            'provider' => 'useraccounts',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,
];