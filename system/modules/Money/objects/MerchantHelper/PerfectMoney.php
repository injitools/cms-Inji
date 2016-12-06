<?php

/**
 * Merchant helper Perfect money
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Money\MerchantHelper;

class PerfectMoney extends \Money\MerchantHelper {

    public static function goToMerchant($payId, $amount, $currency, $description = '', $success = '/', $false = '/') {
        $config = static::getConfig();

        $merchantCurrency = static::getMerchantCurrency($currency);

        $request['PAYEE_ACCOUNT'] = $config['usdWallet'];
        $request['PAYEE_NAME'] = $config['usdWallet'];
        $request['PAYMENT_ID'] = $payId;
        $request['PAYMENT_AMOUNT'] = $amount;
        $request['PAYMENT_UNITS'] = $merchantCurrency->code;
        $request['SUGGESTED_MEMO'] = $description;
        $request['STATUS_URL'] = 'http://' . INJI_DOMAIN_NAME . '/money/merchants/reciver/PerfectMoney';
        $request['PAYMENT_URL'] = $success;
        $request['NOPAYMENT_URL'] = $false;
        $request['PAYMENT_METHOD'] = 'PerfectMoney account';

        $form = new \Ui\Form();
        $form->action = 'https://perfectmoney.is/api/step1.asp';
        $form->begin();
        foreach ($request as $name => $value) {
            $form->input('hidden', $name, '', ['value' => $value]);
        }
        $form->end('process');
        echo '<script>document.querySelector("form").submit();</script>';
    }

    public static function reciver($data, $status) {
        $result = ['status' => 'error'];
        if (empty($_POST['PAYMENT_ID'])) {
            return $result;
        }

        $config = static::getConfig();

        $string = $_POST['PAYMENT_ID'] . ':' . $_POST['PAYEE_ACCOUNT'] . ':' .
                $_POST['PAYMENT_AMOUNT'] . ':' . $_POST['PAYMENT_UNITS'] . ':' .
                $_POST['PAYMENT_BATCH_NUM'] . ':' .
                $_POST['PAYER_ACCOUNT'] . ':' . strtoupper(md5($config['secret'])) . ':' .
                $_POST['TIMESTAMPGMT'];

        $hash = strtoupper(md5($string));
        if ($hash == $_POST['V2_HASH']) {
            $result['payId'] = $data["PAYMENT_ID"];
            $result['status'] = 'success';
        }
        return $result;
    }

}
