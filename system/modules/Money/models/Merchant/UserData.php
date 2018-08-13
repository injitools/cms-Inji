<?php
/**
 * Created by IntelliJ IDEA.
 * User: Inji
 * Date: 06.06.2018
 * Time: 19:14
 */

namespace Money\Merchant;

/**
 * @property int $user_id
 * @property int $merchant_id
 * @property string $code
 * @property string $data
 */
class UserData extends \Model {
    static $cols = [
        'user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'user'],
        'merchant_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'merchant'],
        'code' => ['type' => 'text'],
        'data' => ['type' => 'textarea'],
    ];
}