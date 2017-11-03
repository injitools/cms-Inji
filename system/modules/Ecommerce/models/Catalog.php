<?php
/**
 * INJI
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2017 Alexey Krupskiy
 * @license https://github.com/injitools/Inji/blob/master/LICENSE
 */

namespace Ecommerce;

/**
 * @property int $id
 * @property string $name
 * @property int $weight
 * @property int $parent_id
 * @property int $icon_file_id
 */
class Catalog extends \Model {
    static $cols = [
        'name' => ['type' => 'text'],
        'weight' => ['type' => 'number'],
        'parent_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'parent', 'extraValues' => ['0' => 'Нет родителя']],
        'icon_file_id' => ['type' => 'image'],
        'childsMgr' => ['type' => 'dataManager', 'relation' => 'childs'],
        'categoriesMgr' => ['type' => 'dataManager', 'relation' => 'categories'],
    ];
    static $labels = [
        'name' => 'Название',
        'weight' => 'Вес соритровки',
        'parent_id' => 'Родитель',
        'icon_file_id' => 'Иконка',
        'childsMgr' => 'Дочерние каталоги',
        'categoriesMgr' => 'Категории товаров',
    ];
    static $dataManagers = [
        'manager' => [
            'filters' => ['name', 'parent_id'],
            'cols' => ['name', 'icon_file_id', 'childsMgr', 'categoriesMgr'],
            'sortMode' => true
        ]
    ];
    static $forms = [
        'manager' => [
            'map' => [
                ['name', 'icon_file_id'],
                ['categoriesMgr'],
                ['childsMgr'],

            ]
        ]
    ];

    static function relations() {
        return [
            'parent' => [
                'model' => 'Ecommerce\Catalog',
                'col' => 'parent_id'
            ],
            'childs' => [
                'type' => 'many',
                'col' => 'parent_id',
                'model' => 'Ecommerce\Catalog'
            ],
            'categories' => [
                'type' => 'many',
                'col' => 'catalog_id',
                'model' => 'Ecommerce\Catalog\Category'
            ],
            'icon' => [
                'col' => 'icon_file_id',
                'model' => 'Files\File'
            ]
        ];
    }
}