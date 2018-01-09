<?php

/**
 * Material
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\Materials;

class Material extends \Inji\Model {

    public static $objectName = 'Материал';
    public static $categoryModel = 'Inji\Materials\Category';
    public static $labels = [
        'name' => 'Заголовок',
        'category_id' => 'Раздел',
        'preview' => 'Краткое превью',
        'text' => 'Текст страницы',
        'alias' => 'Алиас страницы',
        'template' => 'Шаблон сайта',
        'viewer' => 'Тип страницы',
        'image_file_id' => 'Фото материала',
        'description' => 'Описание для поисковиков',
        'keywords' => 'Ключевые слова',
        'user_id' => 'Создатель',
        'date_create' => 'Дата создания',
        'date_publish' => 'Дата публикации',
        'tag_list' => 'Теги',
        'default' => 'По умолчанию'
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => [
                'name',
                'alias',
                'default',
                'category_id',
                'tag_list',
                'date_publish',
                'date_create'
            ],
            'sortable' => [
                'name',
                'alias',
                'category_id',
                'date_create'
            ],
            'filters' => [
                'name',
                'preview',
                'text',
                'default',
                'alias',
                'template',
                'viewer',
                'description',
                'keywords',
                'user_id',
                'date_create'
            ],
            'categorys' => [
                'model' => 'Inji\Materials\Category',
            ],
            'sortMode' => true
        ]
    ];
    public static $cols = [
        'name' => ['type' => 'text'],
        'alias' => ['type' => 'text'],
        'preview' => ['type' => 'html'],
        'text' => ['type' => 'html'],
        'keywords' => ['type' => 'text'],
        'description' => ['type' => 'text'],
        'category_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'category'],
        'user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'user'],
        'template' => ['type' => 'select', 'source' => 'method', 'method' => 'templatesList', 'module' => 'Materials'],
        'viewer' => ['type' => 'select', 'source' => 'method', 'method' => 'viewsList', 'module' => 'Materials'],
        'default' => ['type' => 'bool'],
        'hidden' => ['type' => 'bool'],
        'image_file_id' => ['type' => 'image'],
        'link' => ['type' => 'dataManager', 'relation' => 'links'],
        'tree_path' => ['type' => 'text'],
        'weight' => ['type' => 'number'],
        'tag_list' => ['type' => 'text'],
        'date_publish' => ['type' => 'dateTime', 'null' => true, 'emptyValue' => null],
        'date_create' => ['type' => 'dateTime'],
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
                ['name', 'category_id'],
                ['alias', 'image_file_id', 'date_publish'],
                ['template', 'viewer', 'default'],
                ['keywords', 'description'],
                ['tag_list'],
                ['preview'],
                ['text'],
                ['link'],
            ]
        ]
    ];

    public static function relations() {
        return [
            'category' => [
                'model' => 'Inji\Materials\Category',
                'col' => 'category_id'
            ],
            'image' => [
                'model' => 'Inji\Files\File',
                'col' => 'image_file_id'
            ],
            'user' => [
                'model' => 'Inji\Users\User',
                'col' => 'user_id'
            ],
            'links' => [
                'type' => 'many',
                'model' => 'Inji\Materials\Material\Link',
                'col' => 'material_id'
            ]
        ];
    }

    public function getHref() {
        $href = !empty(\Inji\App::$primary->config['defaultModule']) && \Inji\App::$primary->config['defaultModule'] == 'Materials' ? '' : '/materials';
        $treePath = array_filter(explode('/', $this->tree_path));
        if ($treePath) {
            $categorys = Category::getList(['where' => ['id', implode(',', $treePath), 'IN']]);
            foreach ($categorys as $category) {
                if ($category->alias) {
                    $href .= "/{$category->alias}";
                }
            }
        }
        if ((!$href || $href == '/materials') && !$this->alias) {
            return ($href ? $href : '/materials') . '/view/' . $this->pk();
        }
        return $href . "/" . ($this->alias ? $this->alias : $this->pk());
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
            return 'default';
        }
    }

    public function beforeSave() {
        if ($this->default) {
            Material::update(['default', 0]);
        }
    }
}