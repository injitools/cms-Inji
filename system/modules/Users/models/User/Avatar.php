<?php

namespace Users\User;


class Avatar extends \Model {
    static $objectName = 'Аватар';
    static $labels = [
        'image_file_id' => 'Изображение',
        'sex' => 'Пол'
    ];
    static $cols = [
        'image_file_id' => ['type' => 'image'],
        'sex' => ['type' => 'select', 'source' => 'array', 'sourceArray' => [
            '1' => 'Мужской',
            '2' => 'Женский',
        ]],
    ];
    static $dataManagers = [
        'manager' => [
            'cols' => ['image_file_id', 'sex']
        ]
    ];
    static $forms = [
        'manager' => [
            'map' => [['image_file_id', 'sex']]
        ]
    ];

    static function relations() {
        return [
            'image' => [
                'model' => 'Files\File',
                'col' => 'image_file_id'
            ]
        ];
    }
}