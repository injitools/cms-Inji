<?php

/**
 * Reward trigger
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Money\Reward;

class Trigger extends \Inji\Model {

    public static $cols = [
        'reward_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'reward'],
        'type' => ['type' => 'text'],
        'value' => ['type' => 'text'],
        'handler' => ['type' => 'text'],
        'date_create' => ['type' => 'dateTime'],
    ];

    public static function relations() {
        return [
            'params' => [
                'type' => 'many',
                'model' => 'Money\Reward\Trigger\Param',
                'col' => 'reward_trigger_id',
                'resultKey' => 'param'
            ],
            'reward' => [
                'model' => 'Money\Reward',
                'col' => 'reward_id'
            ],
        ];
    }

}
