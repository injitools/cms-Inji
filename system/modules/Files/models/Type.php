<?php

/**
 * Type
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Files;

class Type extends \Model
{
    static $cols = [
        'dir' => ['type' => 'text'],
        'ext' => ['type' => 'text'],
        'group' => ['type' => 'text'],
        'allow_resize' => ['type' => 'bool'],
        'date_create' => ['type' => 'dateTime'],
    ];

}
