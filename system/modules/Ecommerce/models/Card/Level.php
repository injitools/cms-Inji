<?php

/**
 * Card level
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\Ecommerce\Card;

class Level extends \Inji\Model {

    public static $objectName = 'Уровень карты';
    public static $cols = [
        //Основные параметры
        'name' => ['type' => 'text'],
        'sum' => ['type' => 'text'],
        'card_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'card'],
        'discount_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'discount'],
        //Системные параметры
        'date_create' => ['type' => 'dateTime'],
    ];
    public static $labels = [
        'name' => 'Название',
        'card_id' => 'Карта',
        'discount_id' => 'Скидка',
        'sum' => 'Порог накопления',
    ];
    public static $dataManagers = [
        'manager' => [
            'name' => 'Бонусные карты',
            'cols' => [
                'name',
                'sum',
                'card_id',
                'discount_id',
            ],
        ],
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['name', 'sum'],
                ['card_id', 'discount_id'],
            ]
        ]];

    public static function relations() {
        return [
            'card' => [
                'model' => 'Inji\Ecommerce\Card',
                'col' => 'card_id'
            ],
            'discount' => [
                'model' => 'Inji\Ecommerce\Discount',
                'col' => 'discount_id'
            ]
        ];
    }

}
