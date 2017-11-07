<?php

return [
    'access' => [
        'accessTree' => [
            'app' => [
                '_access' => [
                ],
                'deniedUrl' => '/users/login',
            ],
            'appAdmin' => [
                '_access' => [
                    '0' => '3'
                ],
                'deniedUrl' => '/admin/users/login',
            ],
        ],
    ]
];
