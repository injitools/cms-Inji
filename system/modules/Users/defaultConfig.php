<?php

return [
    'access' => [
        'accessTree' => [
            'app' => [
                '_access' => [
                    2, 3
                ],
                'Users' => [
                    'login' => [
                        '_access' => []
                    ],
                    'activation' => [
                        '_access' => []
                    ],
                    'registration' => [
                        '_access' => [1]
                    ],
                    'fastRegistration' => [
                        '_access' => [1]
                    ],
                    'passre' => [
                        '_access' => [1]
                    ],
                ],
                'Social' => [
                    'auth' => [
                        '_access' => []
                    ],
                    'disconnect' => [
                        '_access' => [2, 3]
                    ]
                ]
            ],
            'appAdmin' => [
                'Users' => [
                    'login' => [
                        '_access' => []
                    ]
                ],
            ]
        ]
    ],
    'loginUrl' => [
        'app' => '/',
        'appAdmin' => '/admin'
    ],
    'invites' => false,
    'needActivation' => false,
    'noActivationNotify' => '',
    'noMailNotify' => '',
    'passwordManualSetup' => true,
    'defaultPartner' => 0
];
