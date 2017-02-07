<?php

return [
    'name' => 'Материалы сайта',
    'requires' => [
        'Files', 'Widgets'
    ],
    'migrations' => [
        'addPublishDate' => 'addPublishDate'
    ],
    'menu' => [
        'appAdmin' => [
            [
                'name' => 'Материалы',
                'href' => '/admin/materials/material',
                'childs' => []
            ]
        ]
    ]
];
