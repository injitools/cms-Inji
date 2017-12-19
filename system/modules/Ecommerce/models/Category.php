<?php

/**
 * Item Category
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ecommerce;
/**
 * @property int $id
 * @property int $parent_id
 * @property string $name
 * @property string $alias
 * @property string $viewer
 * @property string $template
 * @property string $description
 * @property int $image_file_id
 * @property int $icon_file_id
 * @property bool $options_inherit
 * @property bool $hidden
 * @property int $views
 * @property bool $imported
 * @property int $weight
 * @property int $user_id
 * @property int $items_count
 * @property string $tree_path
 * @property string $date_create
 *
 * @property-read \Ecommerce\Item[] $items
 * @property-read \Ecommerce\Category $parent
 * @property-read \Ecommerce\Item\Option[] $options
 * @property-read \Files\File $image
 * @property-read \Files\File $icon
 * @property-read \Ecommerce\Category[] $catalogs
 * @property-read \Users\User $user
 */
class Category extends \Model {

    public static $objectName = 'Категория магазина';
    public static $treeCategory = 'Ecommerce\Item';
    public static $cols = [
        //Основные параметры
        'parent_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'parent'],
        'name' => ['type' => 'text'],
        'alias' => ['type' => 'text'],
        'viewer' => ['type' => 'select', 'source' => 'method', 'method' => 'viewsCategoryList', 'module' => 'Ecommerce'],
        'template' => ['type' => 'select', 'source' => 'method', 'method' => 'templatesCategoryList', 'module' => 'Ecommerce'],
        'description' => ['type' => 'html'],
        'image_file_id' => ['type' => 'image'],
        'icon_file_id' => ['type' => 'image'],
        'options_inherit' => ['type' => 'bool'],
        'hidden' => ['type' => 'bool'],
        //Системные
        'views' => ['type' => 'number', 'logging' => false],
        'imported' => ['type' => 'bool'],
        'weight' => ['type' => 'number'],
        'items_count' => ['type' => 'number'],
        'user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'user'],
        'tree_path' => ['type' => 'text'],
        'date_create' => ['type' => 'dateTime'],
        //Менеджеры
        'options' => ['type' => 'dataManager', 'relation' => 'options'],
    ];
    public static $labels = [
        'name' => 'Название',
        'alias' => 'Алиас',
        'parent_id' => 'Родительская категория',
        'icon_file_id' => 'Иконка',
        'image_file_id' => 'Изображение',
        'description' => 'Описание',
        'options_inherit' => 'Наследовать набор свойств',
        'options' => 'Свойства товаров',
        'hidden' => 'Скрытая'
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['name', 'alias'],
                ['parent_id', 'image_file_id', 'icon_file_id'],
                ['viewer', 'template'],
                ['options_inherit', 'hidden'],
                ['options'],
                ['description']
            ]
        ]
    ];

    public static function indexes() {
        return [
            'ecommerce_category_category_parent_id' => [
                'type' => 'INDEX',
                'cols' => [
                    'category_parent_id',
                ]
            ],
            'ecommerce_category_category_tree_path' => [
                'type' => 'INDEX',
                'cols' => [
                    'category_tree_path(255)'
                ]
            ],
        ];
    }

    public static function relations() {
        return [
            'items' => [
                'type' => 'many',
                'model' => 'Ecommerce\Item',
                'col' => 'category_id',
            ],
            'parent' => [
                'model' => 'Ecommerce\Category',
                'col' => 'parent_id'
            ],
            'options' => [
                'type' => 'relModel',
                'model' => 'Ecommerce\Item\Option',
                'relModel' => 'Ecommerce\Item\Option\Relation',
            ],
            'image' => [
                'model' => 'Files\File',
                'col' => 'image_file_id'
            ],
            'icon' => [
                'model' => 'Files\File',
                'col' => 'icon_file_id'
            ],
            'user' => [
                'model' => 'Users\User',
                'col' => 'user_id'
            ],
            'catalogs' => [
                'type' => 'many',
                'model' => 'Ecommerce\Category',
                'col' => 'parent_id',
            ]
        ];
    }

    public static $dataManagers = [
        'manager' => [
            'name' => 'Категории товаров',
            'cols' => [
                'name',
                'image_file_id',
                'parent_id',
            ],
            'sortMode' => true
        ]
    ];

    public function getRoot() {
        $treePath = array_values(array_filter(explode('/', $this->tree_path)));
        if (!empty($treePath[0])) {
            $category = Category::get($treePath[0]);
            if ($category) {
                return $category;
            }
        }
        return $this;
    }

    public function beforeSave() {
        if ($this->id && $this->id == $this->parent_id) {
            $this->parent_id = 0;
            \Msg::add('Категория не может быть сама себе родителем');
        }
    }

    public function beforeDelete() {
        foreach ($this->catalogs as $category) {
            $category->delete();
        }
    }

    public function resolveTemplate() {
        if ($this->template !== 'inherit') {
            return $this->template;
        } elseif ($this->template == 'inherit' && $this->category) {
            return $this->category->resolveTemplate(true);
        } else {
            return (!empty(\App::$cur->ecommerce->config['defaultCategoryTemplate']) ? \App::$cur->ecommerce->config['defaultCategoryTemplate'] : 'current');
        }
    }

    public function resolveViewer() {
        if ($this->viewer !== 'inherit') {
            return $this->viewer;
        } elseif ($this->viewer == 'inherit' && $this->category) {
            return $this->category->resolveViewer(true);
        } else {
            return (!empty(\App::$cur->ecommerce->config['defaultCategoryView']) ? \App::$cur->ecommerce->config['defaultCategoryView'] : 'itemList');
        }
    }

    public function calcItemsCount($save = true) {
        $count = \App::$cur->Ecommerce->getItemsCount(['parent' => $this->id]);
        $this->items_count = $count;
        if ($save) {
            $this->save();
            if ($this->parent) {
                $this->parent->calcItemsCount();
            }
            foreach (\Ecommerce\Catalog::getList(['categories:category_id', $this->id]) as $category) {
                $category->calcItemsCount();
            }
        }
        return $count;
    }
}