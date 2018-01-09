<?php

/**
 * Cart event type
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\Ecommerce\Cart\Event;

class Type extends \Inji\Model {

    public static $cols = [
        'name' => ['type' => 'text']
    ];

}
