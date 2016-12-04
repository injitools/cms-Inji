<?php

/**
 * Notification
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Notifications;

class Notification extends \Model {

    public static $logging = false;
    public static $cols = [
        'name' => ['type' => 'text'],
        'text' => ['type' => 'textarea'],
        'action' => ['type' => 'textarea'],
        'date_create' => ['type' => 'dateTime']
    ];

}