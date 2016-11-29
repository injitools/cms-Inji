<?php

/**
 * City data model
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2016 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Geography\City;

class Data extends \Model {

    public static $objectName = 'Данные города';
    public static $labels = [
        'code' => 'Код',
        'data' => 'Данные',
        'type' => 'Тип',
        'city_id' => 'Город',
        'date_create' => 'Дата создания',
    ];
    public static $cols = [
        'code' => ['type' => 'text'],
        'data' => ['type' => 'textarea'],
        'type' => ['type' => 'text'],
        'city_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'city'],
        'date_create' => ['type' => 'dateTime'],
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => ['code', 'text', 'data', 'date_create']
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['code', 'city_id'],
                ['data']
            ]
        ]
    ];

    public static function relations() {
        return [
            'city' => [
                'model' => 'Geography\City',
                'col' => 'city_id'
            ],
        ];
    }

}
