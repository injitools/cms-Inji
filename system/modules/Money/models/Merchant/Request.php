<?php

/**
 * Merchant request
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Money\Merchant;

class Request extends \Model {

    public static $cols = [
        'post' => ['type' => 'textarea'],
        'get' => ['type' => 'textarea'],
        'result_callback' => ['type' => 'textarea'],
        'system' => ['type' => 'text'],
        'status' => ['type' => 'text'],
        'pay_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'pay'],
        'merchant_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'merchant'],
        'date_create' => ['type' => 'dateTime']
    ];

    public static function relations() {
        return [
            'pay' => [
                'model' => 'Money\Pay',
                'col' => 'pay_id'
            ],
            'merchant' => [
                'model' => 'Money\Merchant',
                'col' => 'merchant_id'
            ],
        ];
    }

}
