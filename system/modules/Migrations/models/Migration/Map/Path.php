<?php

/**
 * Migration map path
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Migrations\Migration\Map;

class Path extends \Model {

    public static $cols = [
        'migration_map_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'map'],
        'parent_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'parent'],
        'object_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'object'],
        'path' => ['type' => 'textarea'],
        'item' => ['type' => 'text'],
        'type' => ['type' => 'text'],
        'date_create' => ['type' => 'dateTime'],
    ];

    public static function relations() {
        return [
            'map' => [
                'model' => 'Migrations\Migration\Map',
                'col' => 'map_id'
            ],
            'object' => [
                'model' => 'Migrations\Migration\ObjectItem',
                'col' => 'object_id'
            ],
            'childs' => [
                'type' => 'many',
                'model' => 'Migrations\Migration\Map\Path',
                'col' => 'parent_id'
            ],
            'parent' => [
                'model' => 'Migrations\Migration\Map\Path',
                'col' => 'parent_id'
            ]
        ];
    }

}
