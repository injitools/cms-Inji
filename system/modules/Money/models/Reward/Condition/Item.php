<?php

/**
 * Reward condition item
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Money\Reward\Condition;

class Item extends \Model {

    public function recivedCount($userId = 0) {
        $userId = $userId ? $userId : \Users\User::$cur->id;
        if (!$userId) {
            return 0;
        }
        $count = 0;
        foreach ($this->recives(['where' => ['user_id', $userId]]) as $recive) {
            if ($recive->expired_date != '0000-00-00 00:00:00' && \DateTime::createFromFormat('Y-m-d H:i:s', $recive->expired_date) <= new \DateTime()) {
                $recive->delete();
                continue;
            }
            $count += $recive->count;
        }
        return $count;
    }

    public static $cols = [
        'reward_condition_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'condition'],
        'type' => ['type' => 'text'],
        'value' => ['type' => 'text'],
        'reciver' => ['type' => 'text'],
        'count' => ['type' => 'number'],
        'expired' => ['type' => 'text'],
    ];
    public static $labels = [
        'reward_condition_id' => 'Условие',
        'type' => 'Тип',
        'value' => 'Значение',
        'reciver' => 'Обработчик',
        'count' => 'Необходимое кол-во',
        'expired' => 'Срок жизни',
    ];
    public static $dataManagers = [
        'manager' => [
            'name' => 'Условия',
            'cols' => ['type', 'value', 'reciver', 'count', 'expired']
        ]
    ];
    public static $forms = [
        'manager' => [
            'name' => 'Условие',
            'map' => [
                ['type', 'value'],
                ['count', 'expired'],
                ['reciver', 'reward_condition_id'],
            ]
        ]
    ];

    public function checkComplete($userId = 0) {
        return $this->count <= $this->recivedCount($userId);
    }

    public static function relations() {
        return [
            'condition' => [
                'model' => 'Money\Reward\Condition',
                'col' => 'reward_condition_id'
            ],
            'recives' => [
                'type' => 'many',
                'model' => 'Money\Reward\Condition\Item\Recive',
                'col' => 'reward_condition_item_id'
            ]
        ];
    }

}
