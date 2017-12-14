<?php

return array(
    'name' => 'Онлайн-магазин',
    'migrations' => [
        'cdekCities' => 'cdekCities',
        'cdekPVZ' => 'cdekPVZ',
    ],
    'requires' => [
        'Money', 'Geography'
    ],
    'menu' => [
        'appAdmin' => [
            [
                'name' => 'Интернет-магазин',
                'href' => '/admin/ecommerce/Item',
                'childs' => [
                    [
                        'name' => 'Список Товаров',
                        'href' => '/admin/ecommerce/Item'
                    ],
                    [
                        'name' => 'Заказы',
                        'href' => '/admin/ecommerce/Cart?datamanagerFilters[cart_status_id][value]=2'
                    ],
                    [
                        'name' => 'Конфигурация',
                        'href' => '/admin/ecommerce/configure'
                    ]
                ]
            ]
        ]
    ]
);
