<?php
/**
 * INJI
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2017 Alexey Krupskiy
 * @license https://github.com/injitools/Inji/blob/master/LICENSE
 */

namespace Inji\Ecommerce;

/**
 * @property int $id
 * @property string $name
 * @property int $weight
 * @property int $parent_id
 * @property int $icon_file_id
 * @property int $items_count
 * @property \Ecommerce\Catalog $parent
 * @property \Ecommerce\Catalog[] $childs
 * @property \Ecommerce\Catalog\Category[] $categories
 * @property \Files\File $icon
 */
class Catalog extends \Inji\Model {
    static $cols = [
        'name' => ['type' => 'text'],
        'weight' => ['type' => 'number'],
        'parent_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'parent', 'extraValues' => ['0' => 'Нет родителя']],
        'icon_file_id' => ['type' => 'image'],
        'items_count' => ['type' => 'number'],
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

    public function calcItemsCount($save = true) {
        $count = 0;
        foreach ($this->categories as $category) {
            $count += $category->category->items_count;
        }
        $this->items_count = $count;
        if ($save) {
            $this->save();
            if ($this->parent) {
                $this->parent->calcItemsCount();
            }
        }
    }

    static function relations() {
        return [
            'parent' => [
                'model' => 'Inji\Ecommerce\Catalog',
                'col' => 'parent_id'
            ],
            'childs' => [
                'type' => 'many',
                'col' => 'parent_id',
                'model' => 'Inji\Ecommerce\Catalog'
            ],
            'categories' => [
                'type' => 'many',
                'col' => 'catalog_id',
                'model' => 'Inji\Ecommerce\Catalog\Category'
            ],
            'icon' => [
                'col' => 'icon_file_id',
                'model' => 'Inji\Files\File'
            ]
        ];
    }
}