<?php

/**
 * Type
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\Files;

class Type extends \Inji\Model {

    public static $cols = [
        'dir' => ['type' => 'text'],
        'ext' => ['type' => 'text'],
        'group' => ['type' => 'text'],
        'allow_resize' => ['type' => 'bool'],
        'options' => ['type' => 'textarea'],
        'date_create' => ['type' => 'dateTime'],
    ];

}
