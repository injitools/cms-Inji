<?php

/**
 * Mode Query
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Exchange1c\Mode;

class Query extends \Exchange1c\Mode {

    public function process() {

        function addToXml($xml, $parent, $nodeName, $text) {
            $node = $parent->appendChild($xml->createElement($nodeName));
            $node->appendChild($xml->createTextNode($text));
            return $node;
        }

        header("Content-Type: text/xml");
        header("Expires: Thu, 19 Feb 1998 13:24:18 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Cache-Control: post-check=0,pre-check=0");
        header("Cache-Control: max-age=0");
        header("Pragma: no-cache");
        $xml = new \DOMDocument('1.0', 'windows-1251');

        $xml->formatOutput = true;

        $root = $xml->createElement('КоммерческаяИнформация');
        $root->setAttribute("ВерсияСхемы", "2.03");
        $root->setAttribute("ДатаФормирования", date('Y-m-d'));
        $root = $xml->appendChild($root);

        $carts = \Ecommerce\Cart::getList(['where' => ['cart_status_id', empty(\App::$cur->Exchange1c->config['uploadStatusId']) ? '3' : \App::$cur->Exchange1c->config['uploadStatusId'], 'IN']]);
        foreach ($carts as $cart) {
            $doc = $xml->createElement('Документ');
            $statusDateTime = new \DateTime($cart->complete_data);
            $items = $cart->cartItems;
            if (!$items) {
                continue;
            }

            $goodss = $xml->createElement('Товары');
            $sum = 0;
            foreach ($items as $cartitem) {
                $goods = $goodss->appendChild($xml->createElement('Товар'));

                $id1c = \App::$cur->migrations->findParse($cartitem->price->offer->item_id, 'Ecommerce\Item');
                if ($id1c) {
                    addToXml($xml, $goods, 'Ид', $id1c->parse_id);
                }
                addToXml($xml, $goods, 'Наименование', $cartitem->item->name);
                $one = addToXml($xml, $goods, 'БазоваяЕдиница', 'шт');
                $one->setAttribute("Код", "796");
                $one->setAttribute("НаименованиеПолное", "Штука");
                $one->setAttribute("МеждународноеСокращение", "PCE");
                addToXml($xml, $goods, 'ЦенаЗаЕдиницу', $cartitem->final_price);
                addToXml($xml, $goods, 'Количество', $cartitem->count);
                addToXml($xml, $goods, 'Сумма', $cartitem->final_price * $cartitem->count);

                $sum += $cartitem->final_price * $cartitem->count;

                $reqs = $goods->appendChild($xml->createElement('ЗначенияРеквизитов'));

                $req = $reqs->appendChild($xml->createElement('ЗначениеРеквизита'));
                addToXml($xml, $req, 'Наименование', 'ВидНоменклатуры');
                addToXml($xml, $req, 'Значение', 'Товар');

                $req = $reqs->appendChild($xml->createElement('ЗначениеРеквизита'));
                addToXml($xml, $req, 'Наименование', 'ТипНоменклатуры');
                addToXml($xml, $req, 'Значение', 'Товар');
            }
            foreach ($cart->extras as $extra) {
                $goods = $goodss->appendChild($xml->createElement('Товар'));
                addToXml($xml, $goods, 'Наименование', $extra->name);
                $one = addToXml($xml, $goods, 'БазоваяЕдиница', 'шт');
                $one->setAttribute("Код", "796");
                $one->setAttribute("НаименованиеПолное", "Штука");
                $one->setAttribute("МеждународноеСокращение", "PCE");
                addToXml($xml, $goods, 'ЦенаЗаЕдиницу', $extra->price);
                addToXml($xml, $goods, 'Количество', $extra->count);
                addToXml($xml, $goods, 'Сумма', $extra->price * $extra->count);
                $reqs = $goods->appendChild($xml->createElement('ЗначенияРеквизитов'));

                $req = $reqs->appendChild($xml->createElement('ЗначениеРеквизита'));
                addToXml($xml, $req, 'Наименование', 'ВидНоменклатуры');
                addToXml($xml, $req, 'Значение', 'Товар');

                $req = $reqs->appendChild($xml->createElement('ЗначениеРеквизита'));
                addToXml($xml, $req, 'Наименование', 'ТипНоменклатуры');
                addToXml($xml, $req, 'Значение', 'Товар');
            }
            if ($cart->delivery && $cart->delivery->price && $sum < $cart->delivery->max_cart_price) {
                $sum += $cart->delivery->price;

                $goods = $goodss->appendChild($xml->createElement('Товар'));
                addToXml($xml, $goods, 'Наименование', 'Доставка');
                $one = addToXml($xml, $goods, 'БазоваяЕдиница', 'шт');
                $one->setAttribute("Код", "796");
                $one->setAttribute("НаименованиеПолное", "Штука");
                $one->setAttribute("МеждународноеСокращение", "PCE");
                addToXml($xml, $goods, 'ЦенаЗаЕдиницу', $cart->delivery->price);
                addToXml($xml, $goods, 'Количество', 1);
                addToXml($xml, $goods, 'Сумма', $cart->delivery->price);
                $reqs = $goods->appendChild($xml->createElement('ЗначенияРеквизитов'));

                $req = $reqs->appendChild($xml->createElement('ЗначениеРеквизита'));
                addToXml($xml, $req, 'Наименование', 'ВидНоменклатуры');
                addToXml($xml, $req, 'Значение', 'Услуга');

                $req = $reqs->appendChild($xml->createElement('ЗначениеРеквизита'));
                addToXml($xml, $req, 'Наименование', 'ТипНоменклатуры');
                addToXml($xml, $req, 'Значение', 'Услуга');
            }

            addToXml($xml, $doc, 'Ид', $cart->id);
            addToXml($xml, $doc, 'Номер', $cart->id);
            addToXml($xml, $doc, 'Дата', $statusDateTime->format('Y-m-d'));
            addToXml($xml, $doc, 'ХозОперация', 'Заказ товара');
            addToXml($xml, $doc, 'Роль', 'Продавец');
            addToXml($xml, $doc, 'Валюта', 'руб');
            addToXml($xml, $doc, 'Курс', '1');
            addToXml($xml, $doc, 'Сумма', $sum);

            $agents = $doc->appendChild($xml->createElement('Контрагенты'));
            $agent = $agents->appendChild($xml->createElement('Контрагент'));
            $user = $cart->user;
            addToXml($xml, $agent, 'Ид', $user->id);
            addToXml($xml, $agent, 'ИдРодителя', $user->parent_id);
            addToXml($xml, $agent, 'Наименование', $user->name());
            addToXml($xml, $agent, 'Роль', $user->role->name);
            addToXml($xml, $agent, 'ПолноеНаименование', $user->name());
            $reg = $agent->appendChild($xml->createElement('АдресРегистрации'));
            addToXml($xml, $reg, 'Представление', '');

            $presents = $agent->appendChild($xml->createElement('Представители'));
            $present = $presents->appendChild($xml->createElement('Представитель'));
            $presentAgent = $present->appendChild($xml->createElement('Контрагент'));

            addToXml($xml, $presentAgent, 'Отношение', 'Контактное лицо');
            addToXml($xml, $presentAgent, 'Ид', $user->id);
            addToXml($xml, $presentAgent, 'Наименование', $user->name());

            addToXml($xml, $doc, 'Время', $statusDateTime->format('H:i:s'));
            addToXml($xml, $doc, 'Комментарий', $cart->comment);
            $goodss = $doc->appendChild($goodss);

            $reqs = $doc->appendChild($xml->createElement('ЗначенияРеквизитов'));
            $req = $reqs->appendChild($xml->createElement('ЗначениеРеквизита'));
            addToXml($xml, $req, 'Наименование', 'Метод оплаты');
            if ($cart->payType) {
                addToXml($xml, $req, 'Значение', $cart->payType->name);
            } else {
                addToXml($xml, $req, 'Значение', 'Наличный расчет');
            }

            $req = $reqs->appendChild($xml->createElement('ЗначениеРеквизита'));
            addToXml($xml, $req, 'Наименование', 'Заказ оплачен');
            addToXml($xml, $req, 'Значение', ($cart->payed) ? 'true' : 'false');

            $req = $reqs->appendChild($xml->createElement('ЗначениеРеквизита'));
            addToXml($xml, $req, 'Наименование', 'Доставка разрешена');
            addToXml($xml, $req, 'Значение', $cart->delivery ? 'true' : 'false');

            $req = $reqs->appendChild($xml->createElement('ЗначениеРеквизита'));
            addToXml($xml, $req, 'Наименование', 'Отменен');
            addToXml($xml, $req, 'Значение', 'false');

            if (!empty(\App::$primary->exchange1c->config['queryCartFieldGroups'])) {
                $req = $reqs->appendChild($xml->createElement('ЗначениеРеквизита'));
                foreach (\App::$primary->exchange1c->config['queryCartFieldGroups'] as $group) {
                    addToXml($xml, $req, 'Наименование', $group['name']);
                    $string = '';
                    foreach ($group['parts'] as $part) {
                        switch ($part['type']) {
                            case 'text':
                                $string .= $part['text'];
                                break;
                            case 'field':
                                $value = \Ecommerce\Cart\Info::get([
                                            ['useradds_field_id', $part['field']],
                                            ['cart_id', $cart->id]
                                ]);
                                $string .= $value->value;
                                break;
                        }
                    }
                    addToXml($xml, $req, 'Значение', $string);
                }
            }
            foreach ($cart->infos as $value) {
                $req = $reqs->appendChild($xml->createElement('ЗначениеРеквизита'));
                addToXml($xml, $req, 'Наименование', $value->name);
                addToXml($xml, $req, 'Значение', $value->value);
            }

            $req = $reqs->appendChild($xml->createElement('ЗначениеРеквизита'));
            addToXml($xml, $req, 'Наименование', 'Статус заказа');
            addToXml($xml, $req, 'Значение', $cart->status->name);

            $req = $reqs->appendChild($xml->createElement('ЗначениеРеквизита'));
            addToXml($xml, $req, 'Наименование', 'Дата изменения статуса');
            addToXml($xml, $req, 'Значение', $statusDateTime->format('Y-m-d H:i:s'));

            $doc = $root->appendChild($doc);
        }

        echo $xml->saveXML();

        $this->end();
    }

}
