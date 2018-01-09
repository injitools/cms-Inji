<?php

/**
 * Social
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\Users;

class Social extends \Inji\Model {

    public static $objectName = "Коннектор с социальной сетью";
    public static $cols = [
        'name' => ['type' => 'text'],
        'code' => ['type' => 'text'],
        'active' => ['type' => 'bool'],
        'image_file_id' => ['type' => 'image'],
        'object_name' => ['type' => 'text'],
        'config' => ['type' => 'dataManager', 'relation' => 'configs'],
        'date_create' => ['type' => 'dateTime']
    ];
    public static $labels = [
        'name' => 'Название',
        'code' => 'Код',
        'config' => 'Настройки',
        'active' => 'Активна',
        'object_name' => 'Название обработчика',
        'date_create' => 'Дата создания'
    ];
    public static $dataManagers = [
        'manager' => [
            'name' => "Коннекторы с социальными сетями",
            'cols' => [
                'name', 'image_file_id', 'active', 'code', 'object_name', 'config', 'date_create'
            ]
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['name', 'code'],
                ['object_name', 'active'],
                ['image_file_id'],
                ['config']
            ]
        ]
    ];

    public static function relations() {
        return [
            'configs' => [
                'type' => 'many',
                'model' => 'Inji\Users\Social\Config',
                'col' => 'social_id'
            ],
            'image' => [
                'model' => 'Inji\Files\File',
                'col' => 'image_file_id'
            ]
        ];
    }

}
