<?php

/**
 * Activity category
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
/*
  1 -
  2 - новый партнер
  3 - изменен статус заказа
  4 - получил награду
 */

namespace Users\Activity;

class Category extends \Model {

    public static $objectName = 'Activity category';
    public static $labels = [
        'name' => 'Название',
    ];
    public static $cols = [
        'name' => ['type' => 'text'],
    ];
    public static $dataManagers = [
        'manager' => [
            'name' => 'Activity',
            'cols' => [
                'name', 'date_create',
            ]
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['name']
            ]
        ]
    ];

}
