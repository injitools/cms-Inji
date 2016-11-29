<?php

/**
 * Unit
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ecommerce;

class Unit extends \Model {

    public static $objectName = 'Единица измерения';
    public static $labels = [
        'name' => 'Название',
        'code' => 'Код',
        'international' => 'Международное обозначение',
    ];
    public static $cols = [
        //Основные параметры
        'name' => ['type' => 'text'],
        'code' => ['type' => 'text'],
        //Системные
        'international' => ['type' => 'text'],
        'date_create' => ['type' => 'dateTime'],
    ];
    public static $dataManagers = [
        'manager' => [
            'name' => 'Единицы измерения',
            'cols' => [
                'name', 'code', 'international'
            ]
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['name', 'code', 'international']
            ]
        ]
    ];

}
