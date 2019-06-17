<?php
/**
 * Created by IntelliJ IDEA.
 * User: Inji
 * Date: 08.06.2018
 * Time: 18:47
 */

namespace Money;

/**
 * @property int $user_id
 * @property int $merchant_id
 * @property int $currency_id
 * @property string $status
 * @property string $comment
 * @property string $ip
 * @property float $amount
 * @property string $info
 */
class Withdraw extends \Model {
    static $cols = [
        'user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'user'],
        'merchant_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'merchant'],
        'currency_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'currency'],
        'status' => ['type' => 'text'],
        'comment' => ['type' => 'text'],
        'ip' => ['type' => 'text'],
        'amount' => ['type' => 'decimal'],
        'info' => ['type' => 'textarea'],
    ];

    static function relations() {
        return [
            'user' => [
                'col' => 'user_id',
                'model' => 'Users\User'
            ],
            'merchant' => [
                'col' => 'merchant_id',
                'model' => 'Money\Merchant'
            ],
            'currency' => [
                'col' => 'currency_id',
                'model' => 'Money\Currency'
            ],
        ];
    }
}