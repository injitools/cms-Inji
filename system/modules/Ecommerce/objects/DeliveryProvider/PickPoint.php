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

use Ecommerce\Delivery\Field\Item;
use Ecommerce\Delivery\Provider\ConfigItem;

class PickPoint extends \Ecommerce\DeliveryProvider {
    static $name = 'PickPoint - курьерская служба';

    /**
     * @param \Ecommerce\Cart $cart
     * @return \Money\Sums
     */
    static function curl_get_file_contents($URL, $data) {
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

    static function calcPrice($cart) {


        $sessionId = \Cache::get('PickPointSession');
        if (!$sessionId) {
            $config = ConfigItem::getList(['where' => ['delivery_provider_id', $cart->delivery->delivery_provider_id], 'key' => 'name']);
            $result = self::curl_get_file_contents('https://e-solution.pickpoint.ru/api/login', [
                'Login' => $config['login']->value,
                'Password' => $config['pass']->value,
            ]);
            $sessionId = json_decode($result, true)['SessionId'];
            \Cache::set('PickPointSession', $sessionId, [], 12 * 60 * 60);
        }
        $city = '';
        foreach ($cart->delivery->fields as $field) {
            if ($field->code === 'index' && !empty($_POST['deliveryFields'][$field->id]) && is_string($_POST['deliveryFields'][$field->id])) {
                $result = json_decode(self::curl_get_file_contents('https://e-solution.pickpoint.ru/api/postindexpostamatlist', ['PostIndex' => $_POST['deliveryFields'][$field->id]]), true);
                //print_r($result['PostamatList'][0]);
                if (!empty($result['PostamatList'][0]['CitiName'])) {
                    $city = $result['PostamatList'][0]['CitiName'];
                    $toId = $result['PostamatList'][0]['Id'];
                }
            }
        }
        if (!$city) {
            return new \Money\Sums([$cart->delivery->currency_id => 0]);
        }
        if ($city === 'Красноярск') {
            $senderCity = 'Красноярск';
        } else {
            $senderCity = 'Москва';
        }
        $result = json_decode(self::curl_get_file_contents('https://e-solution.pickpoint.ru/api/calctariff', [
            'SessionId'=>$sessionId,
            'FromCity'=>$senderCity,
            'IKN'=>$config['ikn']->value,
            'PTNumber'=>$toId,
            'Length'=>25,
            'Depth'=>25,
            'Width'=>25,
        ]), true);
        //print_r($result);
        return new \Money\Sums([$cart->delivery->currency_id => 0]);
    }
}