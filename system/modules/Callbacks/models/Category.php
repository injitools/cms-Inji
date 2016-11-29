<?php

/**
 * Callback Category
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Callbacks;

class Category extends \Model {

    public static $objectName = 'Категория отзывов';
    public static $treeCategory = 'Callbacks\Callback';
    public static $cols = [
        'name' => ['type' => 'text'],
        'alias' => ['type' => 'text'],
        'parent_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'parent'],
        'image_file_id' => ['type' => 'image'],
        'description' => ['type' => 'html'],
        'viewer' => ['type' => 'select', 'source' => 'method', 'method' => 'viewsCategoryList', 'module' => 'Callbacks'],
        'template' => ['type' => 'select', 'source' => 'method', 'method' => 'templatesCategoryList', 'module' => 'Callbacks'],
        'weight' => ['type' => 'number'],
    ];
    public static $labels = [
        'name' => 'Название',
        'alias' => 'Алиас',
        'parent_id' => 'Родительская категория',
        'image_file_id' => 'Изображение',
        'description' => 'Описание',
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['name', 'alias'],
                ['parent_id', 'image_file_id'],
                ['viewer', 'template'],
                ['description']
            ]
        ]
    ];

    public static function relations() {
        return [
            'callbacks' => [
                'type' => 'many',
                'model' => 'Callbacks\Callback',
                'col' => 'category_id',
            ],
            'parent' => [
                'model' => 'Callbacks\Category',
                'col' => 'parent_id'
            ],
            'image' => [
                'model' => 'Files\File',
                'col' => 'image_file_id'
            ],
            'catalogs' => [
                'type' => 'many',
                'model' => 'Callbacks\Category',
                'col' => 'parent_id',
            ]
        ];
    }

    public function resolveTemplate() {
        if ($this->template !== 'inherit') {
            return $this->template;
        } elseif ($this->template == 'inherit' && $this->category) {
            return $this->category->resolveTemplate(true);
        } else {
            return 'current';
        }
    }

    public function resolveViewer() {
        if ($this->viewer !== 'inherit') {
            return $this->viewer;
        } elseif ($this->viewer == 'inherit' && $this->category) {
            return $this->category->resolveViewer(true);
        } else {
            return (!empty(App::$cur->ecommerce->config['defaultCategoryView']) ? App::$cur->ecommerce->config['defaultCategoryView'] : 'itemList');
        }
    }

}
