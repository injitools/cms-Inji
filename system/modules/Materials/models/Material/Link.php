<?php

/**
 * Item name
 *
 * Info
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Materials\Material;

class Link extends \Model
{
    static $objectName = 'Связь с материалом';
    static $labels = [
        'name' => 'Название',
        'material_id' => 'Материал',
        'linked_material_id' => 'Связанный материал',
    ];
    static $dataManagers = [
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
    static $cols = [
        'name' => ['type' => 'text'],
        'material_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'material'],
        'linked_material_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'linkedMaterial'],
    ];
    static $forms = [
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

    static function relations()
    {
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
