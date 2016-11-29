<?php

/**
 * user parent history
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2016 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Users\User;

class ParentHistory extends \Model {

    public static $cols = [
        'user_id' => ['type' => 'number'],
        'old' => ['type' => 'number'],
        'new' => ['type' => 'number'],
        'comment' => ['type' => 'html'],
    ];

}
