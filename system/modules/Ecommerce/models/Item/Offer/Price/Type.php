<?php

/**
 * Item offer price type
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ecommerce\Item\Offer\Price;

class Type extends \Model {

    public static $objectName = 'Тип цены';
    public static $cols = [
        //Основные параметры
        'name' => ['type' => 'text'],
        'curency' => ['type' => 'text'],
        'roles' => ['type' => 'text'],
        'weight' => ['type' => 'number'],
        //Системные
        'date_create' => ['type' => 'dateTime'],
    ];
    public static $labels = [
        'name' => 'Название',
        'curency' => 'Валюта',
        'roles' => 'Роли которым доступна',
        'weight' => 'Вес',
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => [
                'name',
                'curency',
                'roles',
                'weight',
            ],
            'sortMode' => true
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['name', 'curency'],
                ['roles', 'weight'],
            ]
        ]
    ];

}
