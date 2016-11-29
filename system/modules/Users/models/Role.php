<?php

/**
 * Role
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Users;

class Role extends \Model {

    public static $objectName = 'Роль пользователей';
    public static $labels = [
        'name' => 'Название',
        'user' => 'Пользователи',
        'group_id' => 'Группа'
    ];
    public static $cols = [
        'name' => ['type' => 'text'],
        'user' => ['type' => 'dataManager', 'relation' => 'users'],
        'group_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'group'],
    ];
    public static $dataManagers = [
        'manager' => [
            'name' => 'Группы пользователей',
            'cols' => [
                'name', 'group_id', 'user'
            ]
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['name', 'group_id']
            ]
        ]
    ];

    public static function relations() {
        return [
            'group' => [
                'model' => 'Users\Group',
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
