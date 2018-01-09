<?php

/**
 * User inventory item
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2016 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Users\User\Inventory;

class Item extends \Inji\Model {

    public static $cols = [
        'name' => ['type' => 'text'],
        'about' => ['type' => 'textarea'],
        'code' => ['type' => 'text'],
        'image_file_id' => ['type' => 'image'],
        'widget' => ['type' => 'text'],
    ];

}
