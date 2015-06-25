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
                    'passre' => [
                        '_access' => [1]
                    ],
                    'vkAuth' => [
                        '_access' => [1]
                    ]
                ],
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
    ]
];
