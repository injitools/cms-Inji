<?php

/**
 * Ecommerce cart stage
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2016 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ecommerce\Cart;

class Stage extends \Model {

    public static $objectName = 'Этап наполнения корзины';
    public static $cols = [
        'sum' => ['type' => 'decimal'],
        'currency_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'currency'],
        'type' => ['type' => 'select', 'source' => 'array', 'sourceArray' => ['discount' => 'Скидка']],
        'value' => ['type' => 'text'],
        'group' => ['type' => 'text']
    ];
    public static $labels = [
        'sum' => 'Сумма',
        'type' => 'Тип',
        'group' => 'Группа',
        'currency_id' => 'Валюта',
        'value' => 'Значение'
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => ['sum', 'currency_id', 'type', 'value', 'group']
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['sum', 'currency_id'],
                ['type', 'value', 'group']
            ]
        ]
    ];

    public static function relations() {
        return [
            'currency' => [
                'model' => 'Money\Currency',
                'col' => 'currency_id'
            ]
        ];
    }

}
