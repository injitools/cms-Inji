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

    static function deliveryTime($cart) {
        $result = self::request($cart);
        if (!empty($result['Zones'][0])) {
            return [
                'min' => $result['Zones'][0]['DeliveryMin'],
                'max' => $result['Zones'][0]['DeliveryMax']
            ];
        }
        return ['min' => 0, 'max' => 0];
    }

    /* $result = json_decode(self::curl_get_file_contents('https://e-solution.pickpoint.ru/api/CreateShipment', [
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
    static function request($cart) {
        $config = self::config();
        $sessionId = \Cache::get('PickPointSession', [
            'Login' => $config['login'],
            'Password' => $config['pass'],
        ], function ($data) {
            $result = self::curl_get_file_contents('https://e-solution.pickpoint.ru/api/login', $data);
            return json_decode($result, true)['SessionId'];
        }, 12 * 60 * 60);
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
            $fieldInfo = \Ecommerce\UserAdds\Field::get('deliveryfield_city', 'code');
            $field = \Ecommerce\Delivery\Field::get('city', 'code');
            if (isset($cart->infos[$fieldInfo->id])) {
                $item = \Ecommerce\Delivery\Field\Item::get([['id', $cart->infos[$fieldInfo->id]->value], ['delivery_field_id', $field->id]]);
                if ($item) {
                    $data = json_decode($item->data, true);
                    if (!empty($data['PostCodeList'])) {
                        $post = explode(',', $data['PostCodeList'])[0];
                        $result = json_decode(self::curl_get_file_contents('https://e-solution.pickpoint.ru/api/postindexpostamatlist', ['PostIndex' => $post]), true);
                        if (!empty($result['PostamatList'][0]['CitiName'])) {
                            $toId = $result['PostamatList'][0]['Number'];
                        }
                    }

                }
            }
        }
        if (!$toId) {
            return false;
        }

        $senderCity = 'Москва';

        return \Cache::get('pickPointCalc', [
            'SessionId' => $sessionId,
            'IKN' => $config['ikn'],
            'FromCity' => $senderCity,
            'ToPT' => $toId,
        ], function ($data) {
            return json_decode(self::curl_get_file_contents('https://e-solution.pickpoint.ru/api/getzone', $data), true);
        });
    }

    /**
     * @param \Ecommerce\Cart $cart
     * @return \Money\Sums
     */
    static function calcPrice($cart) {
        $result = self::request($cart);
        if (!$result) {
            return new \Money\Sums([$cart->delivery->currency_id => 0]);
        }
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
            $cart->delivery->currency_id => round($zones[$result['Zones'][0]['Zone']] * $result['Zones'][0]['Koeff'] * 1.1, 2)
        ]);
    }

    static function availablePayTypeGroups($cart) {
        return ['online'];
    }
}