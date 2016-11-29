<?php

/**
 * Sitemap controller
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2016 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class SitemapController extends Controller {

    function indexAction() {
        var_dump($this->module->scanModules());
    }

}
