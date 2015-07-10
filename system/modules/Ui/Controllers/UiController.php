<?php

/**
 * Ui helper controller
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class UiController extends Controller {

    function formPopUpAction() {
        if (strpos($_GET['item'], ':')) {
            $raw = explode(':', $_GET['item']);
            $modelName = $raw[0];
            $id = $raw[1];
            $model = $modelName::get($id, $modelName::index(), !empty($_GET['params']['dataManagerParams']) ? $_GET['params']['dataManagerParams'] : []);
        } else {
            $modelName = $_GET['item'];
            $id = null;
            $model = new $modelName();
        }
        if (!empty($_GET['params'])) {
            $params = $_GET['params'];
            if (!empty($params['preset'])) {
                $model->setParams($params['preset']);
            }
        } else {
            $params = [];
        }
        $formName = !empty($_GET['formName']) ? $_GET['formName'] : 'manager';
        $form = new Ui\ActiveForm($model, $formName);
        $form->action = (App::$cur->system ? '/' . App::$cur->name : '') . '/ui/formPopUp/?' . http_build_query($_GET);
        if (!empty($_GET['_']) || !empty($_POST['_'])) {
            $return = new Server\Result();
            ob_start();
            $form->checkRequest($params, true);
            $form->draw($params, true);
            $return->content = ob_get_contents();
            ob_end_clean();
            $return->send();
        } else {
            $form->checkRequest($params);
            if ($model && $model->pk()) {
                $this->view->setTitle('Изменить ' . $modelName::objectName($model));
            } else {
                $presets = !empty($form->form['preset']) ? $form->form['preset'] : [];
                if (!empty($form->form['userGroupPreset'][\Users\User::$cur->group_id])) {
                    $presets = array_merge($presets, $form->form['userGroupPreset'][\Users\User::$cur->group_id]);
                }
                foreach ($presets as $col => $preset) {
                    if (!empty($preset['value'])) {
                        $model->$col = $preset['value'];
                    } elseif (!empty($preset['userCol'])) {
                        if (strpos($preset['userCol'], ':')) {
                            $rel = substr($preset['userCol'], 0, strpos($preset['userCol'], ':'));
                            $param = substr($preset['userCol'], strpos($preset['userCol'], ':') + 1);
                            $model->$col = \Users\User::$cur->$rel->$param;
                        }
                    }
                }
                $this->view->setTitle('Создать ' . $modelName::objectName($model));
            }
            $this->view->page(['content' => 'form', 'data' => compact('form', 'params')]);
        }
    }

    function fastEditAction() {
        $model = $_POST['model']::get($_POST['key']);
        if ($model && $model->checkAccess()) {
            $model->$_POST['col'] = $_POST['data'];
            $model->save();
        }
    }

}
