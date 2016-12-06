<?php

/**
 * Merchant helper Wallet one
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Money\MerchantHelper;

class WalletOne extends \Money\MerchantHelper {

    public static function reciver($data, $status) {
        $config = static::getConfig();
        $skey = $config['secret'];

        // Функция, которая возвращает результат в Единую кассу

        function print_answer($result, $description) {
            $print = "WMI_RESULT=" . strtoupper($result) . "&";
            $print .= "WMI_DESCRIPTION=" . urlencode($description);
            return $print;
        }
        // Проверка наличия необходимых параметров в POST-запросе
        $result = [];
        if (!isset($data["WMI_SIGNATURE"])) {
            $result['callback'] = print_answer("Retry", "Отсутствует параметр WMI_SIGNATURE");
        }

        if (!isset($data["WMI_PAYMENT_NO"])) {
            $result['callback'] = print_answer("Retry", "Отсутствует параметр WMI_PAYMENT_NO");
        }

        if (!isset($data["WMI_ORDER_STATE"])) {
            $result['callback'] = print_answer("Retry", "Отсутствует параметр WMI_ORDER_STATE");
        }

        // Извлечение всех параметров POST-запроса, кроме WMI_SIGNATURE
        $params = [];
        foreach ($data as $name => $value) {
            if ($name !== "WMI_SIGNATURE") {
                $params[$name] = $value;
            }
        }

        // Сортировка массива по именам ключей в порядке возрастания
        // и формирование сообщения, путем объединения значений формы

        uksort($params, "strcasecmp");
        $values = "";

        foreach ($params as $name => $value) {
            //Конвертация из текущей кодировки (UTF-8)
            //необходима только если кодировка магазина отлична от Windows-1251
            $value = iconv("utf-8", "windows-1251", $value);
            $values .= $value;
        }

        // Формирование подписи для сравнения ее с параметром WMI_SIGNATURE

        $signature = base64_encode(pack("H*", md5($values . $skey)));

        //Сравнение полученной подписи с подписью W1

        if (!empty($data["WMI_SIGNATURE"]) && $signature == $data["WMI_SIGNATURE"]) {
            if (strtoupper($data["WMI_ORDER_STATE"]) == "ACCEPTED") {
                // вызываем функцию обработки в случае успеха
                $result['callback'] = print_answer("Ok", "Заказ #" . $data["WMI_PAYMENT_NO"] . " оплачен!");
                $result['payId'] = $data["WMI_PAYMENT_NO"];
                $result['status'] = 'success';
                return $result;
            } else {
                // Случилось что-то странное, пришло неизвестное состояние заказа
                $result['callback'] = print_answer("Retry", "Неверное состояние " . $data["WMI_ORDER_STATE"]);
            }
        } else {
            // Подпись не совпадает, возможно вы поменяли настройки интернет-магазина
            $result['callback'] = print_answer("Retry", "Неверная подпись " . (!empty($data["WMI_SIGNATURE"]) ? $data["WMI_SIGNATURE"] : 'empty'));
        }
        return $result;
    }

    public static function goToMerchant($payId, $amount, $currency, $description = '', $success = '/', $false = '/') {
        $config = static::getConfig();
        $merchantCurrency = static::getMerchantCurrency($currency);

        if (!$description)
            $description = "Оплата заказа на сайте " . idn_to_utf8(INJI_DOMAIN_NAME);

        //Секретный ключ интернет-магазина
        $key = $config['secret'];

        $fields = [];

        // Добавление полей формы в ассоциативный массив
        $fields["WMI_MERCHANT_ID"] = $config['shopId'];
        $fields["WMI_PAYMENT_AMOUNT"] = number_format($amount, 0, '.', '');
        $fields["WMI_CURRENCY_ID"] = $merchantCurrency->code;
        $fields["WMI_PAYMENT_NO"] = $payId;
        $fields["WMI_DESCRIPTION"] = "BASE64:" . base64_encode($description);
        $fields["WMI_EXPIRED_DATE"] = "2019-12-31T23:59:59";
        $fields["WMI_SUCCESS_URL"] = $success;
        $fields["WMI_FAIL_URL"] = $false;
        //Сортировка значений внутри полей
        foreach ($fields as $name => $val) {
            if (is_array($val)) {
                usort($val, "strcasecmp");
                $fields[$name] = $val;
            }
        }

        // Формирование сообщения, путем объединения значений формы,
        // отсортированных по именам ключей в порядке возрастания.
        uksort($fields, "strcasecmp");
        $fieldValues = "";

        foreach ($fields as $value) {
            if (is_array($value)) {
                foreach ($value as $v) {
                    //Конвертация из текущей кодировки (UTF-8)
                    //необходима только если кодировка магазина отлична от Windows-1251
                    $v = iconv("utf-8", "windows-1251", $v);
                }
                $fieldValues .= $v;
            } else {
                //Конвертация из текущей кодировки (UTF-8)
                //необходима только если кодировка магазина отлична от Windows-1251
                $value = iconv("utf-8", "windows-1251", $value);
                $fieldValues .= $value;
            }
        }

        // Формирование значения параметра WMI_SIGNATURE, путем
        // вычисления отпечатка, сформированного выше сообщения,
        // по алгоритму MD5 и представление его в Base64

        $signature = base64_encode(pack("H*", md5($fieldValues . $key)));

        //Добавление параметра WMI_SIGNATURE в словарь параметров формы

        $fields["WMI_SIGNATURE"] = $signature;
        \Tools::redirect('https://www.walletone.com/checkout/default.aspx?' . http_build_query($fields));
    }
}