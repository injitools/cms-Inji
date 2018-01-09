<?php
/**
 * Created by IntelliJ IDEA.
 * User: inji
 * Date: 20.12.2017
 * Time: 16:36
 */

namespace Users\User;


class Avatar extends \Inji\Model {
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
                'model' => 'Inji\Files\File',
                'col' => 'image_file_id'
            ]
        ];
    }
}