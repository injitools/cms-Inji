<?php

/**
 * Menu
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Menu;

class Menu extends \Model {

    public static $objectName = 'Меню';
    public static $labels = [
        'name' => 'Название',
        'code' => 'Алиас',
        'item' => 'Пункты меню',
        'group_id' => 'Группа пользователей',
    ];
    public static $storage = ['type' => 'moduleConfig'];
    public static $cols = [
        'name' => ['type' => 'text'],
        'code' => ['type' => 'text'],
        'item' => ['type' => 'dataManager', 'relation' => 'items'],
        'group_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'group']
    ];
    public static $dataManagers = [
        'manager' => [
            'options' => [
                'access' => [
                    'groups' => [
                        3
                    ]
                ]
            ],
            'cols' => [
                'name', 'code', 'item', 'group_id'
            ]
        ]
    ];
    public static $forms = [
        'manager' => [
            'options' => [
                'access' => [
                    'groups' => [
                        3
                    ]
                ]
            ],
            'map' => [
                ['name', 'code'],
                ['group_id'],
                ['item']
            ]
        ]
    ];

    public static function relations() {
        return [
            'items' => [
                'type' => 'many',
                'model' => 'Menu\Item',
                'col' => 'Menu_id'
            ],
            'group' => [
                'col' => 'group_id',
                'model' => 'Users\Group'
            ]
        ];
    }

    public static function index() {
        return 'id';
    }

}
