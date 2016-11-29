<?php

/**
 * Merchant helper Robokassa
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Money\MerchantHelper;

class Robokassa extends \Money\MerchantHelper {

    public static function reciver($data, $status) {
        $config = static::getConfig();
        $result = [];
        $result['status'] = 'error';
        if (empty($data['InvId'])) {
            return $result;
        }
        $hashGenerated = md5("{$data['OutSum']}:{$data['InvId']}:{$config['pass2']}");

        $result['payId'] = $data["InvId"];

        if (strtolower($data['SignatureValue']) == $hashGenerated)
            $result['status'] = 'success';

        return $result;
    }

    public static function goToMerchant($payId, $amount, $currency, $description = '', $success = '/', $false = '/') {
        $config = static::getConfig();

        $amount = (float) $amount;
        $hash = md5("{$config['login']}:{$amount}:{$payId}:{$config['pass1']}");
        $data = [
            'MrchLogin' => $config['login'],
            'OutSum' => $amount,
            'InvId' => $payId,
            'SignatureValue' => $hash
        ];
        if (empty($config['test'])) {
            \Tools::redirect('https://auth.robokassa.ru/Merchant/Index.aspx?' . http_build_query($data));
        } else {
            \Tools::redirect('http://test.robokassa.ru/Index.aspx?' . http_build_query($data));
        }
    }

}
