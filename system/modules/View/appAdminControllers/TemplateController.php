<?php

/**
 * Template controller
 *
 * This controller help for create and edit template and template pages
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class TemplateController extends \Inji\Controller {

    public function indexAction($templateName) {
        $template = \View\Template::get($templateName, \App::$primary);
        $this->view->setTitle($templateName);
        $this->view->page(['content' => 'Template/edit', 'data' => compact('template')]);
    }

    public function editFileAction($templateName) {
        $template = \View\Template::get($templateName, \App::$primary);
        if (!empty($_GET['path']) && file_exists($template->path . '/' . Tools::parsePath($_GET['path']))) {
            $code = file_get_contents("php://input");
            if (!empty($code)) {
                $result = new Server\Result();
                $result->successMsg = 'Файл сохранен';
                file_put_contents($template->path . '/' . Tools::parsePath($_GET['path']), $code);
                $result->send();
            }
            $content = file_get_contents($template->path . '/' . Tools::parsePath($_GET['path']));
            $this->libs->loadLib('Ace');
            $this->view->page(['content' => 'Template/edit', 'data' => compact('template', 'content')]);
        } else {
            $this->view->page(['content' => 'Template/chooseFile', 'data' => compact('template')]);
        }
    }

}
