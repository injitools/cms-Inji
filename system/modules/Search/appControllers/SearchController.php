<?php

/**
 * INJI
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2017 Alexey Krupskiy
 * @license https://github.com/injitools/Inji/blob/master/LICENSE
 */
class SearchController extends Controller {
    function indexAction() {
        //$_GET['search']
        $this->view->setTitle('Поиск по сайту');
        $map = [];
        if (!empty($_GET['search']) && is_string($_GET['search'])) {

            $modules = Module::getInstalled(App::$primary);

            foreach ($modules as $module) {
                if (method_exists(App::$cur->$module, 'siteSearch')) {
                    $map[$module] = App::$cur->$module->siteSearch($_GET['search']);
                }
            }
        }
        $this->view->page(['data' => ['map' => $map]]);
    }
}