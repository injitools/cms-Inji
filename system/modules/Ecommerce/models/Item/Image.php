<?php

/**
 * Item image
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2016 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ecommerce\Item;

class Image extends \Model {

    public static $objectName = 'Фото товара';
    public static $labels = [
        'file_id' => 'Изображение',
        'item_id' => 'Товар',
        'name' => 'Название',
        'description' => 'Описание',
    ];
    public static $cols = [
        'file_id' => ['type' => 'image'],
        'item_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'item'],
        'name' => ['type' => 'text'],
        'description' => ['type' => 'html'],
        'weight' => ['type' => 'number'],
    ];

    public static function relations() {
        return [
            'item' => [
                'col' => 'item_id',
                'model' => 'Ecommerce\Item'
            ],
            'file' => [
                'col' => 'file_id',
                'model' => 'Files\File'
            ]
        ];
    }

    public static $dataManagers = [
        'manager' => [
            'name' => 'Фото товара',
            'cols' => [
                'file_id', 'name'
            ]
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['name', 'file_id'],
                ['description']
            ]
        ]
    ];

    public function beforeSave() {
        if ($this->file_id && !$this->item->image_file_id) {
            $item = $this->item;
            $item->image_file_id = $this->file_id;
            $item->save();
        }
    }

    public function beforeDelete() {
        if ($this->file) {
            $this->file->delete();
        }
    }
}