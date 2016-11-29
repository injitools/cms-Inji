<?php

/**
 * Social config
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Users\Social;

class Config extends \Model {

    public static $objectName = "Опция коннектора с социальной сетью";
    public static $cols = [
        'name' => ['type' => 'text'],
        'value' => ['type' => 'text'],
        'social_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'social'],
        'date_create' => ['type' => 'dateTime']
    ];
    public static $labels = [
        'name' => 'Опция',
        'value' => 'Значение',
        'date_create' => 'Дата создания'
    ];
    public static $dataManagers = [
        'manager' => [
            'name' => "Опции коннектора с социальными сетями",
            'cols' => [
                'name', 'value', 'date_create'
            ]
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['name'],
                ['value'],
            ]
        ]
    ];

    public static function relations() {
        return [
            'social' => [
                'model' => 'Users\Social',
                'col' => 'social_id'
            ]
        ];
    }

}
