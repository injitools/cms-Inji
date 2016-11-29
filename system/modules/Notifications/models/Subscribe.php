<?php

/**
 * Subscribe
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Notifications;

class Subscribe extends \Model {

    public static function relations() {
        return [
            'chanel' => [
                'model' => 'Notifications\Chanel',
                'col' => 'chanel_id'
            ]
        ];
    }

}
