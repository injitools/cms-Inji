<?php

/**
 * Model for card level
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ecommerce\Card;

class Level extends \Model {

    static $objectName = 'Уровень карты';
    static $cols = [
        'name' => ['type' => 'text'],
        'sum' => ['type' => 'text'],
        'card_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'card'],
        'discount_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'discount'],
    ];
    static $labels = [
        'name' => 'Название',
        'card_id' => 'Карта',
        'discount_id' => 'Скидка',
        'sum' => 'Порог накопления',
    ];
    static $dataManagers = [
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
    static $forms = [
        'manager' => [
            'map' => [
                ['name', 'sum'],
                ['card_id', 'discount_id'],
            ]
    ]];

    static function relations() {
        return [
            'card' => [
                'model' => 'Ecommerce\Card',
                'col' => 'card_id'
            ],
            'discount' => [
                'model' => 'Ecommerce\Discount',
                'col' => 'discount_id'
            ]
        ];
    }

}
