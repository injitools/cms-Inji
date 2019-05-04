<?php

/**
 * Merchant helper
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Money;

class MerchantHelper {

    public static $merchant;

    public static function getMerchant() {
        if (!self::$merchant) {
            $class = get_called_class();
            $class = substr($class, strrpos($class, '\\') + 1);
            self::$merchant = Merchant::get($class, 'object_name');
        }
        return self::$merchant;
    }

    public static function getConfig() {
        $merchant = self::getMerchant();
        $configs = [];
        foreach ($merchant->configs as $config) {
            $configs[$config->name] = $config->value;
        }
        return $configs;
    }

    public static function getMerchantCurrency($currency) {
        $merchant = self::getMerchant();
        foreach ($merchant->currencies as $merchantCurrency) {
            if ($merchantCurrency->currency_id = $currency->id) {
                return $merchantCurrency;
            }
        }
    }

    public static function getFinalSum($pay, $method) {
        switch ($method['type']) {
            case 'transfer':
                $sum = $pay->sum / $method['transfer']->rate;
                break;
            default:
                $sum = $pay->sum;
                break;
        }
        return $sum;
    }

    public static function showDepositForm($currencyId = 0) {
        \App::$cur->view->widget('Money\depositForms/primary', [
            'helper' => static::class,
            'currencyId' => $currencyId
        ]);
    }
}
