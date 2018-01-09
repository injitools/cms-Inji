<?php

/**
 * Category
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\Materials;

class Category extends \Inji\Model {

    public static $objectName = 'Категория';
    public static $treeCategory = 'Inji\Materials\Material';
    public static $labels = [
        'name' => 'Название',
        'description' => 'Описание',
        'image_file_id' => 'Изображение',
        'parent_id' => 'Родительская категория',
        'alias' => 'Алиас',
        'viewer' => 'Тип категории по умолчанию',
        'template' => 'Шаблон категории по умолчанию',
        'material_viewer' => 'Тип страниц по умолчанию',
        'material_template' => 'Шаблон страниц по умолчанию',
    ];
    public static $cols = [

        'name' => ['type' => 'text'],
        'alias' => ['type' => 'text'],
        'image_file_id' => ['type' => 'image'],
        'description' => ['type' => 'html'],
        'parent_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'parent', 'showCol' => 'name'],
        'user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'user'],
        'material_viewer' => ['type' => 'select', 'source' => 'method', 'method' => 'viewsList', 'module' => 'Materials'],
        'material_template' => ['type' => 'select', 'source' => 'method', 'method' => 'templatesList', 'module' => 'Materials'],
        'viewer' => ['type' => 'select', 'source' => 'method', 'method' => 'viewsCategoryList', 'module' => 'Materials'],
        'template' => ['type' => 'select', 'source' => 'method', 'method' => 'templatesCategoryList', 'module' => 'Materials'],
        'tree_path' => ['type' => 'text'],
        'weight' => ['type' => 'number'],
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
                'name',
                'alias',
                'parent_id',
            ],
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
                ['name', 'parent_id'],
                ['alias', 'image_file_id'],
                ['viewer', 'template'],
                ['material_viewer', 'material_template'],
                ['description'],
            ]
        ]
    ];

    public function beforeDelete() {
        foreach ($this->childs as $child) {
            $child->delete();
        }
    }

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

    public function getHref() {
        $href = !empty(\App::$primary->config['defaultModule']) && \App::$primary->config['defaultModule'] == 'Materials' ? '/category' : '/materials/category';
        $treePath = array_filter(explode('/', $this->tree_path));
        if ($treePath) {
            $categorys = Category::getList(['where' => ['id', implode(',', $treePath), 'IN']]);
            foreach ($categorys as $category) {
                $href .= "/{$category->alias}";
            }
        }
        return $href . "/" . ($this->alias ? $this->alias : $this->pk());
    }

    public static function relations() {
        return [
            'parent' => [
                'model' => 'Inji\Materials\Category',
                'col' => 'parent_id'
            ],
            'childs' => [
                'type' => 'many',
                'model' => 'Inji\Materials\Category',
                'col' => 'parent_id'
            ],
            'items' => [
                'type' => 'many',
                'model' => 'Inji\Materials\Material',
                'col' => 'category_id'
            ],
            'image' => [
                'model' => 'Inji\Files\File',
                'col' => 'image_file_id'
            ],
            'user' => [
                'model' => 'Inji\Users\User',
                'col' => 'user_id'
            ]
        ];
    }

    public function resolveTemplate($material = false) {
        $param = $material ? 'material_template' : 'template';
        if ($this->$param !== 'inherit') {
            return $this->$param;
        } elseif ($this->$param == 'inherit' && $this->parent) {
            return $this->parent->resolveTemplate($material);
        } else {
            return 'current';
        }
    }

    public function resolveViewer($material = false) {
        $param = $material ? 'material_viewer' : 'viewer';
        if ($this->$param !== 'inherit') {
            return $this->$param;
        } elseif ($this->$param == 'inherit' && $this->parent) {
            return $this->parent->resolveViewer($material);
        } else {
            return $material ? 'default' : 'category';
        }
    }

}
