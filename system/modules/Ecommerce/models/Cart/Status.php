<?php

/**
 * Cart Status
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\Ecommerce\Cart;

class Status extends \Inji\Model {

    public static $objectName = 'Статус корзины';
    public static $cols = [
        //Основные параметры
        'name' => ['type' => 'text'],
        'type' => ['type' => 'text'],
        'code' => ['type' => 'text'],
        //Системные
        'date_create' => ['type' => 'dateTime'],
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => [
                'name', 'type', 'code', 'date_create'
            ]
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['name'],
                ['type', 'code']
            ]
        ]
    ];

}
