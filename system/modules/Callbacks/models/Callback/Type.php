<?php

/**
 * Callback Type
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Callbacks\Callback;

class Type extends \Model {

    public static $objectName = "Тип отзыва";
    public static $cols = [
        'name' => ['type' => 'text'],
    ];
    public static $labels = [
        'name' => 'Название',
        'date_create' => 'Дата создания'
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => [
                'name', 'date_create'
            ],
            'filters' => [
                'name'
            ]
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['name'],
            ]
        ]
    ];

}
