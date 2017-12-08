<?php

/**
 * Activity
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Users;

class Activity extends \Model {
    public static $logging = false;
    public static $objectName = 'Activity';
    public static $labels = [
        'text' => 'Текст',
        'user_id' => 'Пользователь',
        'category_id' => 'Категория',
    ];
    public static $cols = [
        'text' => ['type' => 'text'],
        'user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'user'],
        'category_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'category'],
        'date_create' => ['type' => 'dateTime'],
    ];
    public static $dataManagers = [
        'manager' => [
            'name' => 'Activity',
            'cols' => [
                'text', 'user_id', 'category_id', 'date_create',
            ]
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['text'],
                ['user_id', 'category_id']
            ]
        ]
    ];

    public static function relations() {
        return [
            'user' => [
                'model' => 'Users\User',
                'col' => 'user_id'
            ],
            'category' => [
                'model' => 'Users\Activity\Category',
                'col' => 'category_id'
            ],
        ];
    }

}
