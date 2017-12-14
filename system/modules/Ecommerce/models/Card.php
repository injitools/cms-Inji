<?php

/**
 * Card
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ecommerce;

class Card extends \Model {

    public static $objectName = 'Карта';
    public static $cols = [
        //Основные параметры
        'name' => ['type' => 'text'],
        'price' => ['type' => 'text'],
        'item_offer_price_id' => ['type' => 'number'],
        'image_file_id' => ['type' => 'image'],
        //Системные параметры
        'date_create' => ['type' => 'dateTime'],
        //Менеджеры
        'level' => ['type' => 'dataManager', 'relation' => 'levels'],
    ];
    public static $labels = [
        'name' => 'Название',
        'price' => 'Стоимость',
        'level' => 'Уровни',
        'image_file_id' => 'Изображение',
    ];
    public static $dataManagers = [
        'manager' => [
            'name' => 'Бонусные карты',
            'cols' => [
                'name',
                'price',
                'level'
            ],
        ],
    ];
    public static $forms = [
        'manager' => [
            'inputs' => [
                'cardSearch' => [
                    'type' => 'search',
                    'source' => 'relation',
                    'relation' => 'price',
                    'label' => 'Карта',
                    'cols' => [
                        'offer:item:search_index'
                    ],
                    'col' => 'item_offer_price_id',
                ],
            ],
            'map' => [
                ['name', 'price'],
                ['cardSearch','image_file_id'],
                ['level'],
            ]
        ]];

    public static function relations() {
        return [
            'levels' => [
                'type' => 'many',
                'model' => 'Ecommerce\Card\Level',
                'col' => 'card_id'
            ],
            'image' => [
                'model' => 'Files\File',
                'col' => 'image_file_id'
            ],
            'price' => [
                'model' => 'Ecommerce\Item\Offer\Price',
                'col' => 'item_offer_price_id'
            ]
        ];
    }

}
