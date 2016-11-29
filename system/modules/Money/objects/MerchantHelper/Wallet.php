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

class Wallet extends \Money\MerchantHelper {

    public static function reciver($data, $status) {
        return false;
    }

    public static function goToMerchant($payId, $amount, $currency, $description = '', $success = '/', $false = '/') {
        $wallets = \App::$cur->money->getUserWallets();
        \Tools::redirect('/money/walletPay/' . $payId . '/' . $wallets[$currency->id]->id);
    }

}
