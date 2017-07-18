<?php

/**
 * Warehouse
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ecommerce;

class Warehouse extends \Model {

    public static $objectName = 'Склад';
    public static $labels = [
        'name' => 'Название',
    ];
    public static $cols = [
        //Основные параметры
        'name' => ['type' => 'text'],
        'type' => ['type' => 'text'],
        //Системные
        'date_create' => ['type' => 'dateTime'],
    ];
    public static $dataManagers = [
        'manager' => [
            'name' => 'Склады',
            'cols' => [
                'name',
                'type',
            ],
        ],
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['name', 'type'],
            ]
        ]];

}
