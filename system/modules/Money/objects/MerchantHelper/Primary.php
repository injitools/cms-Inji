<?php

/**
 * Merchant helper personal wallet
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Money\MerchantHelper;

class Primary extends \Money\MerchantHelper {

    public static function reciver($data, $status) {
        return false;
    }

    public static function goToMerchant($payId, $amount, $currency, $description = '', $success = '/', $false = '/') {
        \Tools::redirect('/money/primaryPay/' . $payId . '/' . $currency->id);
    }

    public static function getFinalSum($pay, $method) {
        $sum = parent::getFinalSum($pay, $method);
        if ($pay->data && $cart = \Ecommerce\Cart::get($pay->data)) {
            $extra = '0.' . (strlen((string) $cart->id) > 1 ? substr((string) $cart->id, -2) : $cart->id);
        } else {
            $extra = 0;
        }
        return $sum + $extra;
    }

}
