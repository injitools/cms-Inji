<?php

/**
 * Menu admin controller
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class MenuController extends adminController {

    public function indexAction($appType = 'app') {
        $this->view->setTitle('Меню сайта');
        if (!$appType) {
            $appType = App::$cur->type;
        }
        $this->view->page(['data' => compact('appType')]);
    }

}
