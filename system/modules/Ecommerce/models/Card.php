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
        'item_offer_id' => ['type' => 'number'],
        'image_file_id' => ['type' => 'image'],
        //Системные параметры
        'date_create' => ['type' => 'dateTime'],
        //Менеджеры
        'level' => ['type' => 'dataManager', 'relation' => 'levels'],
        'pricesMgr' => ['type' => 'dataManager', 'relation' => 'prices'],
    ];
    public static $labels = [
        'name' => 'Название',
        'price' => 'Стоимость',
        'level' => 'Уровни',
        'image_file_id' => 'Изображение',
        'prices' => 'Типы цен',
    ];
    public static $dataManagers = [
        'manager' => [
            'name' => 'Бонусные карты',
            'cols' => [
                'name',
                'price',
                'level',
                'pricesMgr',
            ],
        ],
    ];
    public static $forms = [
        'manager' => [
            'inputs' => [
                'cardSearch' => [
                    'type' => 'search',
                    'source' => 'relation',
                    'relation' => 'offer',
                    'label' => 'Товарное предложение карты в магазине',
                    'cols' => [
                        'item:search_index'
                    ],
                    'col' => 'item_offer_id',
                ],
            ],
            'map' => [
                ['name', 'price'],
                ['cardSearch', 'image_file_id'],
                ['level'],
                ['pricesMgr'],
            ]
        ]];

    public static function relations() {
        return [
            'levels' => [
                'type' => 'many',
                'model' => 'Ecommerce\Card\Level',
                'col' => 'card_id'
            ],
            'prices' => [
                'type' => 'relModel',
                'model' => 'Ecommerce\Item\Offer\Price\Type',
                'relModel' => 'Ecommerce\Card\PriceTypeLink'
            ],
            'image' => [
                'model' => 'Files\File',
                'col' => 'image_file_id'
            ],
            'offer' => [
                'model' => 'Ecommerce\Item\Offer',
                'col' => 'item_offer_id'
            ]
        ];
    }

}
