<?php

/**
 * Device
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Notifications\Subscriber;

/**
 * Description of Device
 *
 * @author inji
 */
class Device extends \Model
{
    static function relations()
    {
        return [
            'subscriber' => [
                'model' => 'Notifications\Subscriber',
                'col' => 'subscriber_id'
            ]
        ];
    }

}