<?php

/**
 * Merchant helper Payeer
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Money\MerchantHelper;

class Payeer extends \Money\MerchantHelper {

    public static function goToMerchant($payId, $amount, $currency, $description = '', $success = '/', $false = '/') {
        $config = static::getConfig();

        $data['m_shop'] = $config['shopId'];
        $data['m_orderid'] = $payId;
        $data['m_amount'] = number_format($amount, 2, '.', '');
        $data['m_curr'] = 'RUB';
        $data['m_desc'] = base64_encode($description);
        $data['m_key'] = $config['secret'];
        $data['m_process'] = 'send';

        $arHash = array(
            $data['m_shop'],
            $data['m_orderid'],
            $data['m_amount'],
            $data['m_curr'],
            $data['m_desc'],
            $data['m_key']
        );
        $data['m_sign'] = strtoupper(hash('sha256', implode(':', $arHash)));
        \Tools::redirect('http://payeer.com/merchant/?' . http_build_query($data));
    }

    public static function reciver($data, $status) {
        $config = static::getConfig();

        $result['status'] = 'error';
        if (isset($_POST['m_operation_id']) && isset($_POST['m_sign'])) {
            $m_key = $config['secret'];
            $arHash = array($_POST['m_operation_id'],
                $_POST['m_operation_ps'],
                $_POST['m_operation_date'],
                $_POST['m_operation_pay_date'],
                $_POST['m_shop'],
                $_POST['m_orderid'],
                $_POST['m_amount'],
                $_POST['m_curr'],
                $_POST['m_desc'],
                $_POST['m_status'],
                $m_key);
            $sign_hash = strtoupper(hash('sha256', implode(':', $arHash)));
            if ($_POST['m_sign'] == $sign_hash && $_POST['m_status'] == 'success') {
                $result['callback'] = $_POST['m_orderid'] . '|success';
                $result['payId'] = $data["m_orderid"];
                $result['status'] = 'success';
            } else {
                $result['callback'] = $_POST['m_orderid'] . '|error';
            }
        }

        return $result;
    }

}
