<?php

return [
    'storage' => [
        'appTypeSplit' => true,
        'appAdmin' => [
            'scheme' => [
                'Menu' => [
                    'ai' => 2
                ],
                'Item' => [
                    'ai' => 6
                ]
            ],
            'Menu' => [
                '0' => [
                    'id' => 1,
                    'name' => 'Меню боковой панели',
                    'code' => 'sidebarMenu'
                ]
            ],
            'Item' => [
                [
                    'id' => 4,
                    'type' => 'href',
                    'name' => 'Основные настройки',
                    'href' => '/admin/siteConfig',
                    'Menu_id' => 1,
                ],
                [
                    'id' => 1,
                    'type' => 'href',
                    'name' => 'Меню',
                    'href' => '/admin/menu',
                    'Menu_id' => 1
                ],
                [
                    'id' => 2,
                    'type' => 'href',
                    'name' => 'Модули',
                    'href' => '/admin/modules',
                    'Menu_id' => 1
                ],
                [
                    'id' => 3,
                    'type' => 'href',
                    'name' => 'Темы оформления',
                    'href' => '/admin/view',
                    'Menu_id' => 1
                ],
            ]
        ]
    ]
];
