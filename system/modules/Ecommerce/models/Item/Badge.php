<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Inji\Ecommerce\Item;

/**
 * Description of Badge
 *
 * @author benzu
 */
class Badge extends \Inji\Model {

    public static $cols = [
        'name' => ['type' => 'text'],
        'image_file_id' => ['type' => 'image'],
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['name', 'image_file_id']
            ]
        ]
    ];
    public static $labels = [
        'name' => 'Название',
        'image_file_id' => 'Изображение бейджа'
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => ['name', 'image_file_id']
        ]
    ];

    public static function relations() {
        return [
            'image' => [
                'model' => 'Inji\Files\File',
                'col' => 'image_file_id'
            ]
        ];
    }
}