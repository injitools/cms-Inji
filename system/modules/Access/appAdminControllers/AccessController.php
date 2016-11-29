<?php

/**
 * Access controller
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2016 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class AccessController extends adminController {

    function indexAction() {
        $this->view->setTitle('Настройка доступа к разделам сайта');
        $defaultConfig = $this->module->config['access']['accessTree'];
        $modules = Module::getInstalled(\App::$cur->primary);
        $this->view->page(['data' => compact('modules', 'defaultConfig')]);
    }

}
