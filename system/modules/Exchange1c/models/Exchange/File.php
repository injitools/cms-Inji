<?php

/**
 * Exchange File
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Exchange1c\Exchange;

class File extends \Model
{
    public static $cols = [
        'name' => ['type' => 'text'],
        'size' => ['type' => 'number'],
        'date_create' => ['type' => 'dateTime'],
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => [
                'name', 'size', 'date_create'
            ],
        ]
    ];

}
