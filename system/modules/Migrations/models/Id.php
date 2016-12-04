<?php

/**
 * Id
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Migrations;

class Id extends \Model {

    public static $logging = false;
    public static $cols = [
        'object_id' => ['type' => 'number'],
        'type' => ['type' => 'text'],
        'parse_id' => ['type' => 'text'],
    ];

}