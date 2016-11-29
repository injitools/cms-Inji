<?php

/**
 * View app controller
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class ViewController extends Controller {

    public function editorcssAction() {
        if (file_exists($this->view->template->path . '/css/editor.css')) {
            Tools::redirect('/static/templates/' . $this->view->template['name'] . '/css/editor.css');
        } else {
            header("Content-type: text/css");
            exit();
        }
    }

    public function templateProgramAction() {
        $args = func_get_args();
        if ($args) {
            $moduleName = ucfirst($args[0]);
            $params = array_slice($args, 1);
            if (file_exists($this->view->template->path . '/program/modules/' . $moduleName . '/' . $moduleName . '.php')) {
                include_once $this->view->template->path . '/program/modules/' . $moduleName . '/' . $moduleName . '.php';
                $module = new $moduleName($this->module->app);
                $cotroller = $module->findController();
                $cotroller->params = $params;
                $cotroller->run();
            }
        }
    }

}
