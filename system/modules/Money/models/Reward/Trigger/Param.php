<?php

/**
 * Item name
 *
 * Info
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2016 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Money\Reward\Trigger;

class Param extends \Model {

    public static $cols = [
        'reward_trigger_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'trigger'],
        'param' => ['type' => 'text'],
        'value' => ['type' => 'text'],
    ];

    public static function relations() {
        return [
            'trigger' => [
                'model' => 'Money\Reward\Trigger',
                'col' => 'reward_trigger_id'
            ],
        ];
    }

}
