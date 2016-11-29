<?php

/**
 * Material link
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Materials\Material;

class Link extends \Model {

    public static $objectName = 'Связь с материалом';
    public static $labels = [
        'name' => 'Название',
        'material_id' => 'Материал',
        'linked_material_id' => 'Связанный материал',
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => [
                'name',
                'material_id',
                'linked_material_id',
            ],
            'sortable' => [
                'name',
                'material_id',
                'linked_material_id',
            ],
            'sortMode' => true
        ]
    ];
    public static $cols = [
        'material_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'material'],
        'linked_material_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'linkedMaterial'],
        'name' => ['type' => 'text'],
        'weight' => ['type' => 'number'],
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
                ['name'],
                ['material_id', 'linked_material_id'],
            ]
        ]
    ];

    public static function relations() {
        return [
            'material' => [
                'model' => '\Materials\Material',
                'col' => 'material_id'
            ],
            'linkedMaterial' => [
                'model' => '\Materials\Material',
                'col' => 'linked_material_id'
            ],
        ];
    }

}
