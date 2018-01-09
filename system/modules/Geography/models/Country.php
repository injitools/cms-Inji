<?php

/**
 * Country model
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2016 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Geography;

class Country extends \Inji\Model {

    public static $objectName = 'Страна';
    public static $labels = [
        'name' => 'Название',
        'city' => 'Город',
        'date_create' => 'Дата создания',
    ];
    public static $cols = [
        'name' => ['type' => 'text'],
        'city' => ['type' => 'dataManager', 'relation' => 'citys'],
        'date_create' => ['type' => 'dateTime'],
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => ['name', 'city', 'date_create']
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['name']
            ]
        ]
    ];

    public static function relations() {
        return [
            'citys' => [
                'type' => 'many',
                'model' => 'Geography\City',
                'col' => 'country_id'
            ]
        ];
    }

}
