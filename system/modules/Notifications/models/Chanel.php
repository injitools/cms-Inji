<?php

/**
 * Chanel
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Notifications;

class Chanel extends \Inji\Model {

    public static $cols = [
        'name' => ['type' => 'text'],
        'alias' => ['type' => 'text'],
        'path' => ['type' => 'text'],
        'parent_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'parent'],
        'date_create' => ['type' => 'dateTime']
    ];

    public static function relations() {
        return [
            'parent' => [
                'model' => 'Notifications\Chanel',
                'col' => 'parent_id'
            ]
        ];
    }
}