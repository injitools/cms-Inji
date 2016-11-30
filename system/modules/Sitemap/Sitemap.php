<?php

/**
 * Sitemap
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2016 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class Sitemap extends Module {

    function scanModules() {
        $modules = Module::getInstalled(App::$primary);
        $map = [];
        foreach ($modules as $module) {
            $map[$module] = App::$cur->$module->sitemap();
            if (!$map[$module]) {
                unset($map[$module]);
            }
        }
        return $map;
    }

    function generate($map) {
        header("Content-Type: text/xml");
        header("Expires: Thu, 19 Feb 1998 13:24:18 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Cache-Control: post-check=0,pre-check=0");
        header("Cache-Control: max-age=0");
        header("Pragma: no-cache");

        $xml = new \DOMDocument('1.0', 'utf-8');
        $xml->formatOutput = true;
        $root = $xml->createElement('urlset');
        $root->setAttribute("xmlns", "http://www.sitemaps.org/schemas/sitemap/0.9");
        $root = $xml->appendChild($root);

        $addToXml = function($xml, $parent, $nodeName, $text) {
            $node = $parent->appendChild($xml->createElement($nodeName));
            $node->appendChild($xml->createTextNode($text));
            return $node;
        };

        foreach ($map as $module => $items) {
            foreach ($items as $item) {
                $url = $xml->createElement('url');
                foreach ($item['url'] as $key => $item) {
                    $addToXml($xml, $url, $key, $item);
                }
                $root->appendChild($url);
            }
        }
        echo $xml->saveXML();
    }

}
