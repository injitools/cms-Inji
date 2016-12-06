<?php

/**
 * Yandex export controller
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2016 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class YandexExportController extends Controller {

    public function indexAction() {

        /**
         * @param DOMDocument $xml
         * @param string $nodeName
         */
        function addToXml($xml, $parent, $nodeName, $text) {
            $node = $parent->appendChild($xml->createElement($nodeName));
            $node->appendChild($xml->createTextNode($text));
            return $node;
        }

        $imp = new DOMImplementation;

        $dtd = $imp->createDocumentType('yml_catalog', '', 'shops.dtd');

        $xml = $imp->createDocument('', '', $dtd);

        $xml->encoding = 'UTF-8';

        $xml->formatOutput = true;

        $root = $xml->createElement('yml_catalog');
        $root->setAttribute("date", date('Y-m-d H:i'));
        $root = $xml->appendChild($root);

        $shop = $xml->createElement('shop');

        addToXml($xml, $shop, 'name', \App::$cur->config['site']['name']);
        addToXml($xml, $shop, 'company', \App::$cur->config['site']['company_name']);
        addToXml($xml, $shop, 'url', 'http://' . INJI_DOMAIN_NAME);

        $currencies = $xml->createElement('currencies');
        $currency = $currencies->appendChild($xml->createElement('currency'));
        $currency->setAttribute('id', 'RUR');
        $currency->setAttribute('rate', '1');
        $currency->setAttribute('plus', '');
        $shop->appendChild($currencies);

        $categories = $xml->createElement('categories');
        foreach (Ecommerce\Category::getList() as $category) {
            $xmlCategory = addToXml($xml, $categories, 'category', $category->name);
            $xmlCategory->setAttribute('id', $category->id);
            if ($category->parent_id) {
                $xmlCategory->setAttribute('parentId', $category->parent_id);
            }
        }
        $shop->appendChild($categories);

        addToXml($xml, $shop, 'local_delivery_cost', '300');

        $offers = $xml->createElement('offers');
        foreach (App::$cur->ecommerce->getItems() as $item) {
            $offer = $offers->appendChild($xml->createElement('offer'));
            addToXml($xml, $offer, 'url', 'http://' . INJI_DOMAIN_NAME . '/ecommerce/view/' . $item->id);
            addToXml($xml, $offer, 'price', $item->getPrice()->price);
            addToXml($xml, $offer, 'currencyId', 'RUR');
            addToXml($xml, $offer, 'categoryId', $item->category_id);
            addToXml($xml, $offer, 'delivery', 'true');
            addToXml($xml, $offer, 'local_delivery_cost', '300');
            addToXml($xml, $offer, 'vendor', 'vendor');
            addToXml($xml, $offer, 'model', 'model');
            addToXml($xml, $offer, 'description', $item->description);
            addToXml($xml, $offer, 'manufacturer_warranty', 'true');
            addToXml($xml, $offer, 'country_of_origin', 'Китай');

            foreach ($item->options as $option) {
                $param = addToXml($xml, $offer, 'param', $option->value);
                $param->setAttribute('name', $option->item_option_name);
                if ($option->item_option_postfix) {
                    $param->setAttribute('unit', $option->item_option_postfix);
                }
            }
        }
        $shop->appendChild($offers);

        $root->appendChild($shop);

        header("Content-Type: text/xml");
        header("Expires: Thu, 19 Feb 1998 13:24:18 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Cache-Control: post-check=0,pre-check=0");
        header("Cache-Control: max-age=0");
        header("Pragma: no-cache");
        echo $xml->saveXML();
    }

}
