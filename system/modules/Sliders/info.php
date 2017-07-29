<?php

return [
    'name' => 'Слайдеры',
    'requires' => [
        'Files'
    ],
    'widgets' => [
        'Sliders\slider' => [
            'name' => 'Слайдер',
            'params' => [
                ['name'=>'Выберите', 'type' => 'select', 'source' => 'model', 'model' => 'Sliders\Slider']
            ]
        ]
    ],
    'menu' => [
        'appAdmin' => [
            [
                'name' => 'Слайдеры',
                'href' => '/admin/Sliders/Slider',
            ]
        ]
    ]
];
