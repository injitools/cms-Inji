<?php

/**
 * Ui controller
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class UiController extends Controller {

    public function formPopUpAction() {
        if (strpos($_GET['item'], ':')) {
            $raw = explode(':', $_GET['item']);
            $modelName = $raw[0];
            $id = $raw[1];
            $model = $modelName::get($id, $modelName::index(), !empty($_GET['params']['dataManagerParams']) ? $_GET['params']['dataManagerParams'] : []);
        } else {
            $modelName = $_GET['item'];
            $model = new $modelName();
        }
        $params = [];
        if (!empty($_GET['params'])) {
            $params = $_GET['params'];
            if (!empty($params['preset'])) {
                $model->setParams($params['preset']);
            }
        }
        if (!empty($_GET['params']['dataManagerParams']['appType'])) {
            $params['appType'] = $_GET['params']['dataManagerParams']['appType'];
        }

        $formName = !empty($_GET['formName']) ? $_GET['formName'] : (!empty($_GET['params']['formName']) ? $_GET['params']['formName'] : 'manager');
        $form = new Ui\ActiveForm($model, $formName);
        if (!empty($_GET['_']) || !empty($_POST['_'])) {
            $return = new Server\Result();
            ob_start();
            $form->checkRequest($params, true);
            $_GET['item'] = get_class($form->model) . ($model->pk() ? ':' . $model->pk() : '');
            $get = $_GET;
            if (isset($get['notSave'])) {
                unset($get['notSave']);
            }
            $form->action = (App::$cur->system ? '/' . App::$cur->name : '') . '/ui/formPopUp/?' . http_build_query($get);
            $form->draw($params, true);
            $return->content = ob_get_contents();
            ob_end_clean();
            $return->send();
        } else {
            $form->checkRequest($params);
            $_GET['item'] = get_class($form->model) . ($model->pk() ? ':' . $model->pk() : '');
            $get = $_GET;
            if (isset($get['notSave'])) {
                unset($get['notSave']);
            }
            $form->action = (App::$cur->system ? '/' . App::$cur->name : '') . '/ui/formPopUp/?' . http_build_query($get);
            $this->view->setTitle(($model && $model->pk() ? 'Изменить ' : 'Создать ') . $form->header);
            $this->view->page(['content' => 'form', 'data' => compact('form', 'params')]);
        }
    }

    public function fastEditAction() {
        $model = $_POST['model']::get($_POST['key']);
        if ($model && $model->checkAccess()) {
            $model->$_POST['col'] = $_POST['data'];
            $model->save();
        }
    }

    public function autocompleteAction() {
        $snippets = $this->module->getSnippets('autocomplete');
        if (!is_string($_GET['snippet']) || !isset($snippets[$_GET['snippet']])) {
            exit();
        }
        $snippet = $snippets[$_GET['snippet']];
        $result = new \Server\Result();
        $result->content = $snippet['find']($_GET['search'], $_GET['snippetParams']);
        $result->send();
    }
}
