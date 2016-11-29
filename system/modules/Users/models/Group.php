<?php

/**
 * Group
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Users;

class Group extends \Model {

    public static $objectName = 'Группа пользователей';
    public static $labels = [
        'name' => 'Название',
        'user' => 'Пользователи',
        'role' => 'Роли'
    ];
    public static $cols = [
        'name' => ['type' => 'text'],
        'user' => ['type' => 'select', 'source' => 'relation', 'relation' => 'users'],
        'role' => ['type' => 'select', 'source' => 'relation', 'relation' => 'roles'],
    ];
    public static $dataManagers = [
        'manager' => [
            'name' => 'Группы пользователей',
            'cols' => [
                'name', 'role', 'user'
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

    public static function relations() {
        return [
            'roles' => [
                'type' => 'many',
                'model' => 'Users\Role',
                'col' => 'group_id'
            ],
            'users' => [
                'type' => 'many',
                'model' => 'Users\User',
                'col' => 'group_id'
            ]
        ];
    }

}
