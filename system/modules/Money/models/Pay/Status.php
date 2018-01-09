<?php

/**
 * Pay Status
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Money\Pay;

class Status extends \Inji\Model {

    public static $cols = [

        'name' => ['type' => 'text'],
        'code' => ['type' => 'text'],
        'date_create' => ['type' => 'dateTime']
    ];

}
