<?php

/**
 * Item
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Menu;

class Item extends \Model {

    public static $objectName = 'Пункт меню';
    public static $labels = [
        'type' => 'Тип',
        'name' => 'Название',
        'tooltip' => 'Подсказка',
        'href' => 'Ссылка',
        'Menu_id' => 'Меню',
        'parent_id' => 'Дочерний пункт',
        'child' => 'Подпункты'
    ];
    public static $storage = ['type' => 'moduleConfig'];
    public static $cols = [
        'type' => ['type' => 'select', 'source' => 'array', 'sourceArray' => [
                'href' => 'Ссылка',
            ]
        ],
        'aditional' => ['type' => 'hidden'],
        'name' => ['type' => 'text'],
        'href' => ['type' => 'text'],
        'tooltip' => ['type' => 'text'],
        'weight' => ['type' => 'number'],
        'Menu_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'menu'],
        'parent_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'parent'],
        'child' => ['type' => 'dataManager', 'relation' => 'childs'],
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => [
                'type',
                'name',
                'tooltip',
                'href',
                'child',
                'Menu_id'
            ],
            'sortMode' => true
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['aditional'],
                ['type', 'Menu_id'],
                ['name', 'href'],
                ['parent_id', 'tooltip'],
                ['child']
            ]
        ]
    ];

    public static function relations() {
        return [
            'menu' => [
                'model' => 'Menu\Menu',
                'col' => 'Menu_id'
            ],
            'childs' => [
                'type' => 'many',
                'model' => 'Menu\Item',
                'col' => 'parent_id'
            ],
            'parent' => [
                'model' => 'Menu\Item',
                'col' => 'parent_id'
            ]
        ];
    }

    public static function index() {
        return 'id';
    }

}
