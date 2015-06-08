<?php

namespace Materials;

class Material extends \Model {

    static $labels = [
        'material_name' => 'Заголовок',
        'material_catalog_id' => 'Раздел',
        'material_preview' => 'Краткое превью',
        'material_text' => 'Текст страницы',
        'material_chpu' => 'Алиас страницы',
        'material_template' => 'Шаблон сайта',
        'material_viewer' => 'Тип страницы'
    ];
    static $dataManagers = [
        'manager' => [
            'cols' => [
                'material_name',
                'material_chpu',
                'material_catalog_id',
            ],
            'categorys' => [
                'model' => 'Materials\Catalog',
            ]
        ]
    ];
    static $cols = [
        'material_name' => ['type' => 'text'],
        'material_chpu' => ['type' => 'text'],
        'material_viewer' => ['type' => 'select', 'source' => 'method', 'method' => 'viewsList', 'module' => 'Materials'],
        'material_template' => ['type' => 'select', 'source' => 'method', 'method' => 'templatesList', 'module' => 'Materials'],
        'material_preview' => ['type' => 'html'],
        'material_text' => ['type' => 'html'],
        'material_catalog_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'catalog', 'showCol' => 'mc_name'],
    ];
    static $forms = [
        'manager' => [
            'map' => [
                ['material_name', 'material_catalog_id'],
                ['material_chpu'],
                ['material_template', 'material_viewer'],
                ['material_preview'],
                ['material_text'],
            ]
        ]
    ];

    static function relations() {
        return [
            'catalog' => [
                'model' => 'Materials\Catalog',
                'col' => 'material_catalog_id'
            ]
        ];
    }

    function beforeSave() {
        if ($this->catalog) {
            $this->material_tree_path = $this->catalog->catalog_tree_path . $this->catalog->mc_id . '/';
        } else {
            $this->material_tree_path = '/';
        }
    }

}