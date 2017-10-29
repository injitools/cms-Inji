<?php
/**
 * INJI
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2017 Alexey Krupskiy
 * @license https://github.com/injitools/Inji/blob/master/LICENSE
 */

namespace Ecommerce\DeliveryProvider;

use Ecommerce\Delivery\Provider\ConfigItem;

class PickPoint extends \Ecommerce\DeliveryProvider {
    static $name = 'PickPoint - курьерская служба';

    /**
     * @param \Ecommerce\Cart $cart
     * @return \Money\Sums
     */
    static function curl_get_file_contents($URL, $data) {
        //var_dump($URL,$data);
        $xml = json_encode($data);
        $headers = array(
            "Content-type: text/json",
            "Content-length: " . strlen($xml),
            "Connection: close",
        );
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $URL);
        curl_setopt($c, CURLOPT_POST, 1);
        curl_setopt($c, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($c, CURLOPT_HTTPHEADER, $headers);
        $contents = curl_exec($c);
        curl_close($c);

        if ($contents) return $contents;
        else return FALSE;
    }

    /**
     * @param \Ecommerce\Cart $cart
     * @return \Money\Sums
     */
    static function calcPrice($cart) {

        $config = ConfigItem::getList(['where' => ['delivery_provider_id', $cart->delivery->delivery_provider_id], 'key' => 'name']);
        $sessionId = \Cache::get('PickPointSession', []);
        if (!$sessionId) {
            $result = self::curl_get_file_contents('https://e-solution.pickpoint.ru/api/login', [
                'Login' => $config['login']->value,
                'Password' => $config['pass']->value,
            ]);
            $sessionId = json_decode($result, true)['SessionId'];
            \Cache::set('PickPointSession', [], $sessionId, 12 * 60 * 60);
        }
        $toId = '';
        foreach ($cart->delivery->fields as $field) {
            if ($field->code === 'pickpoint') {
                if (!empty($_POST['deliveryFields'][$field->id]) && is_string($_POST['deliveryFields'][$field->id])) {
                    $toId = $_POST['deliveryFields'][$field->id];
                } elseif (isset($cart->deliveryInfos[$field->id])) {
                    $toId = $cart->deliveryInfos[$field->id]->value;
                }
                break;
            }
        }
        if (!$toId) {
            return new \Money\Sums([$cart->delivery->currency_id => 0]);
        }

        $senderCity = 'Москва';

        /** $result = json_decode(self::curl_get_file_contents('https://e-solution.pickpoint.ru/api/CreateShipment', [
         * 'SessionId' => $sessionId,
         * 'Sendings' => [[
         * 'IKN' => $config['ikn']->value,
         * 'Invoice' => [
         * 'Description' => 'test',
         * 'PostamatNumber' => $toId,
         * 'MobilePhone' => '+79999999999',
         * 'PostageType' => '10003',
         * 'GettingType' => '101',
         * 'PayType' => 1,
         * 'Sum' => '1000'
         * ]
         * ]],
         * ]), true);
         * var_dump($result);
         * $result = json_decode(self::curl_get_file_contents('https://e-solution.pickpoint.ru/api/calctariff', [
         * 'SessionId' => $sessionId,
         * 'FromCity' => $senderCity,
         * 'IKN' => $config['ikn']->value,
         * 'PTNumber' => $toId,
         * 'Length' => 25,
         * 'Depth' => 25,
         * 'Width' => 25,
         * 'Weight' => 0.5,
         * ]), true);
         *
         * $summ = 0;
         * var_dump($result['Services']);
         * foreach ($result['Services'] as $service) {
         * $summ = $service['Tariff'] + $service['NDS'];
         * }
         * //$result = json_decode(self::curl_get_file_contents('https://e-solution.pickpoint.ru/api/cancelInvoice', ['SessionId' => $sessionId, 'InvoiceNumber' => '15938160323']), true);
         */
        /*
         * 'Error' => string '' (length=0)
  'ErrorCode' => int 0
  'Zones' =>
    array (size=1)
      0 =>
        array (size=7)
          'DeliveryMax' => int 3
          'DeliveryMin' => int 2
          'FromCity' => string 'Москва' (length=12)
          'Koeff' => float 1.25
          'ToCity' => string 'Пушкин' (length=12)
          'ToPT' => string '4702-023' (length=8)
          'Zone' => string '0' (length=1)
         */
        $result = json_decode(self::curl_get_file_contents('https://e-solution.pickpoint.ru/api/getzone', [
            'SessionId' => $sessionId,
            'IKN' => $config['ikn']->value,
            'FromCity' => $senderCity,
            'ToPT' => $toId,
        ]), true);
        //var_dump($config['ikn']->value, $result);
        $zones = [
            0 => 270,
            1 => 292,
            2 => 299,
            3 => 315,
            4 => 328,
            5 => 368,
            6 => 443,
            7 => 466,
            8 => 527,
        ];
        return new \Money\Sums([
            $cart->delivery->currency_id => $zones[$result['Zones'][0]['Zone']] * $result['Zones'][0]['Koeff'] * 1.1
        ]);
    }
}