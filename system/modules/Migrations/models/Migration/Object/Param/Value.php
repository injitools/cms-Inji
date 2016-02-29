<?php

/**
 * Migration object param value
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Migrations\Migration\Object\Param;

class Value extends \Model
{
    public static $cols = [
        'origianl' => ['type' => 'textarea'],
        'replace' => ['type' => 'textarea'],
        'param_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'param'],
        'date_create' => ['type' => 'dateTime'],
    ];

    public static function relations()
    {
        return [
            'param' => [
                'col' => 'param_id',
                'model' => 'Migrations\Migration\Object\Param'
            ]
        ];
    }

}
