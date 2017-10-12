<?php

/**
 * Data manager controller
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class DataManagerController extends Controller {

    public function parseRequest() {
        $return = [];
        $return['params'] = UserRequest::get('params', 'array', []);

        $item = UserRequest::get('modelName', 'string', '');
        if (!$item) {
            $item = UserRequest::get('item', 'string', '');
        }

        if (strpos($item, ':')) {
            $raw = explode(':', $item);
            $return['modelName'] = $raw[0];
            $return['model'] = $return['modelName']::get($raw[1], $return['modelName']::index(), $return['params']);
        } else {
            $return['modelName'] = $item;
            $return['model'] = null;
        }

        if (!empty($return['params']['relation'])) {
            $relation = $return['modelName']::getRelation($return['params']['relation']);
            if (!empty($relation['type']) && $relation['type'] == 'relModel') {
                $return['modelName'] = $relation['relModel'];
            } else {
                $return['modelName'] = $relation['model'];
            }
        }
        $return['params']['filters'] = UserRequest::get('filters', 'array', []);
        $return['params']['sortered'] = UserRequest::get('sortered', 'array', []);
        $return['params']['mode'] = UserRequest::get('mode', 'string', '');
        $return['params']['all'] = UserRequest::get('all', 'bool', false);

        $return['key'] = UserRequest::get('key', 'int', 0);
        $return['col'] = UserRequest::get('col', 'string', '');
        $return['col_value'] = UserRequest::get('col_value', 'string', '');

        $return['action'] = UserRequest::get('action', 'string', '');
        $return['ids'] = trim(UserRequest::get('ids', 'string', ''), ',');
        $return['adInfo'] = UserRequest::get('adInfo', 'array', []);

        $return['download'] = UserRequest::get('download', 'bool', false);
        $return['silence'] = UserRequest::get('silence', 'bool', false);

        $return['managerName'] = UserRequest::get('managerName', 'string', 'manager');
        if (!empty($return['params']['managerName'])) {
            $return['managerName'] = $return['params']['managerName'];
        }
        if (!$return['managerName']) {
            $return['managerName'] = 'manager';
        }

        return $return;
    }

    public function indexAction() {
        $result = new Server\Result();

        ob_start();

        $request = $this->parseRequest();

        $dataManager = new Ui\DataManager($request['modelName'], $request['managerName']);
        $dataManager->draw($request['params'], $request['model']);

        $result->content = ob_get_contents();

        ob_end_clean();

        $result->send();
    }

    public function loadRowsAction() {
        $result = new Server\Result();
        $result->content = [];

        ob_start();

        $request = $this->parseRequest();

        $dataManager = new Ui\DataManager($request['modelName'], $request['managerName']);
        if ($request['download']) {

            ini_set('memory_limit', '2000M');
            set_time_limit(0);

            $request['params']['all'] = true;
            $request['params']['download'] = true;
            ob_end_clean();
            header('Content-Encoding: UTF-8');
            header("Content-Type: text/csv");
            header("Content-Disposition: attachment; filename=" . str_replace(' ', '_', $request['modelName']::$objectName ? $request['modelName']::$objectName : $request['modelName']) . ".csv");
            echo "\xEF\xBB\xBF"; // UTF-8 BOM


            $cols = $dataManager->getCols();
            if ($dataManager->getActions(true)) {
                $cols = array_slice($cols, 1);
            }
            $endRow = true;
            foreach ($cols as $colName => $options) {
                if (!$endRow) {
                    echo ";";
                }
                $endRow = false;
                echo '"' . $options['label'] . '"';
            }
            echo "\n";
            $endRow = true;
        }
        if (!$request['params']['all']) {
            $pages = $dataManager->getPages($request['params'], $request['model']);
            $request['params']['page'] = !empty($pages->params['page']) ? $pages->params['page'] : $dataManager->page;
            $request['params']['limit'] = !empty($pages->params['limit']) ? $pages->params['limit'] : $dataManager->limit;
        }
        $rows = $dataManager->getRows($request['params'], $request['model']);
        foreach ($rows as $row) {
            if ($request['download']) {
                foreach ($row as $col) {
                    if (!$endRow) {
                        echo ";";
                    }
                    $endRow = false;
                    echo '"' . str_replace(["\n", '"'], ['“'], $col) . '"';
                }
                echo "\n";
                $endRow = true;
            } else {
                Ui\Table::drawRow($row);
            }
        }
        if ($request['download']) {
            exit();
        }

        $result->content['rows'] = ob_get_contents();
        ob_clean();

        $result->content['pages'] = '';

        if (isset($pages) && $pages) {
            if ($pages) {
                $pages->draw();
                echo '<div style="background:#fff;">записей: <b>' . $pages->options['count'] . '</b>. страница <b>' . $pages->params['page'] . '</b> из <b>' . $pages->params['pages'] . '</b></div>';
            }
            $result->content['pages'] = ob_get_contents();
            ob_end_clean();
        }
        $result->send();
    }

    public function loadCategorysAction() {
        $result = new Server\Result();

        ob_start();

        $request = $this->parseRequest();

        $dataManager = new Ui\DataManager($request['modelName'], $request['managerName']);
        $dataManager->drawCategorys();

        $result->content = ob_get_contents();
        ob_end_clean();

        $result->send();
    }

    public function delRowAction() {

        $request = $this->parseRequest();

        $dataManager = new Ui\DataManager($request['modelName'], $request['managerName']);

        if ($dataManager->checkAccess()) {
            $model = $request['modelName']::get($request['key'], $request['modelName']::index(), $request['params']);
            if ($model) {
                $model->delete($request['params']);
            }
        }
        $result = new Server\Result();
        $result->successMsg = empty($request['silence']) ? 'Запись удалена' : '';
        $result->send();
    }

    public function updateRowAction() {

        $request = $this->parseRequest();

        $dataManager = new Ui\DataManager($request['modelName'], $request['managerName']);

        if ($dataManager->checkAccess()) {
            $model = $request['modelName']::get($request['key'], $request['modelName']::index(), $request['params']);
            if ($model) {
                $model->{$request['col']} = $request['col_value'];
                $model->save($request['params']);
            }
        }
        $result = new Server\Result();
        $result->successMsg = empty($request['silence']) ? 'Запись Обновлена' : '';
        $result->send();
    }

    public function groupActionAction() {
        $request = $this->parseRequest();
        $dataManager = new Ui\DataManager($request['modelName'], $request['managerName']);
        $result = new Server\Result();
        $result->success = false;
        $result->content = 'Не удалось выполнить операцию';
        if ($dataManager->checkAccess()) {
            $ids = trim($request['ids'], ' ,');
            if ($request['action'] && $ids) {
                $actions = $dataManager->getActions();
                if (!empty($actions[$request['action']])) {
                    $actionParams = $actions[$request['action']];
                    if (!empty($actionParams['access']['groups']) && !in_array(\Users\User::$cur->group_id, $actionParams['access']['groups'])) {
                        $result->content = 'У вас нет прав доступа к операции ' . (!isset($actionParams['name']) ? $actionParams['className']::$name : $actionParams['name']);
                    } else {
                        try {
                            $result->successMsg = $actionParams['className']::groupAction($dataManager, $ids, $actionParams, !empty($_GET['adInfo']) ? $_GET['adInfo'] : []);
                            $result->success = true;
                        } catch (\Exception $e) {
                            $result->content = $e->getMessage();
                        }
                    }
                }
            }
        } else {
            $result->content = 'У вас нет прав доступа к менеджеру ' . $request['managerName'] . ' модели ' . $request['modelName'];
        }
        $result->send();
    }

    public function delCategoryAction() {

        $request = $this->parseRequest();

        $dataManager = new Ui\DataManager($request['modelName'], $request['managerName']);

        if ($dataManager->checkAccess() && !empty($dataManager->managerOptions['categorys'])) {
            $categoryModel = $dataManager->managerOptions['categorys']['model'];
            $model = $categoryModel::get($request['key'], $categoryModel::index(), $request['params']);
            if ($model) {
                $model->delete($request['params']);
            }
        }
        $result = new Server\Result();
        $result->send();
    }
}