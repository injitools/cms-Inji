<?php

/**
 * Merchant helper Paykeeper
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Money\MerchantHelper;


class Paykeeper extends \Money\MerchantHelper {

    public static function goToMerchant($payId, $amount, $currency, $description = '', $success = '/', $false = '/') {
        $config = static::getConfig();
        # предполагаются заранее известными значения переменных
        # $client_login, $order_id, $order_sum и $optional_phone

        $payment_parameters = http_build_query(array(
            "clientid" => \Users\User::$cur->id,
            "orderid" => $payId,
            "sum" => $amount,
            //"phone" => $optional_phone
        ));
        $options = array("http" => array(
            "method" => "POST",
            "header" =>
                "Content-type: application/x-www-form-urlencoded",
            "content" => $payment_parameters
        ));
        $context = stream_context_create($options);
        echo "<style>.tmg {margin:0 auto}</style>";
        echo file_get_contents("http://{$config['domain']}.paykeeper.ru/order/inline/", FALSE, $context);

    }

    public static function reciver($data, $status) {
        $config = static::getConfig();
        $result = [];
        $result['status'] = 'error';

        $secret_seed = $config['secret'];
        $id = $_POST['id'];
        $sum = $_POST['sum'];
        $clientid = $_POST['clientid'];
        $orderid = $_POST['orderid'];
        $key = $_POST['key'];

        if ($key != md5($id . sprintf("%.2lf", $sum) .
                $clientid . $orderid . $secret_seed)
        ) {
            $result['callback'] = 'Error! Hash mismatch';
            return $result;
        }

        if ($orderid == "") {
            # Платёж – пополнение счёта, нужно зачислить   деньги на баланс $clientid
            # ...
        } else {
            $result['payId'] = $data["orderid"];
            $result['status'] = 'success';
        }
        $result['callback'] = "OK " . md5($id . $secret_seed);


        return $result;
    }
}