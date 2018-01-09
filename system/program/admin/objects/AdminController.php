<?php

namespace Inji\Admin;
/**
 * App admin controller
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class AdminController extends \Inji\Controller {

    public function indexAction() {
        $args = func_get_args();
        call_user_func_array([$this, 'dataManagerAction'], $args);
    }

    public function dataManagerAction($model = '', $dataManager = 'manager') {
        if (!$model) {
            $modulePath = \Inji\Module::getModulePath($this->module->name);
            $path = $modulePath . '/models';
            if (file_exists($path)) {
                $files = array_slice(scandir($path), 2);
                foreach ($files as $file) {
                    if (is_dir($path . '/' . $file)) {
                        continue;
                    }
                    $model = pathinfo($file, PATHINFO_FILENAME);
                    break;
                }
            }
        }
        $modelName = $this->module->name . '\\' . ucfirst($model);
        $possibleModels = [];
        if ($this->module->app->namespace) {
            $possibleModels[] = $this->module->app->namespace . '\\' . $modelName;
        }
        $possibleModels[] = 'Inji\\' . $modelName;
        $fullModelName = false;
        foreach ($possibleModels as $possibleModel) {
            if (class_exists($possibleModel)) {
                $fullModelName = $possibleModel;
                break;
            }
        }
        $dataManager = new  \Inji\Ui\DataManager($fullModelName, $dataManager);
        $title = !empty($dataManager->managerOptions['name']) ? $dataManager->managerOptions['name'] : $fullModelName::objectName();
        $this->view->setTitle($title);
        $this->view->page(['module' => 'Ui', 'content' => 'dataManager/manager', 'data' => compact('dataManager')]);
    }

    public function viewAction($model, $pk) {
        $fullModelName = $this->module->name . '\\' . ucfirst($model);
        if (\Inji\Users\User::$cur->group_id != 3 && (empty($fullModelName::$views['manager']['access']) || !in_array(\Inji\Users\User::$cur->group_id, $fullModelName::$views['manager']['access']['groups']))) {
            \Inji\Tools::redirect('/admin', 'У вас нет прав доступа для просмотра этого объекта', 'danger');
        }
        $item = $fullModelName::get($pk);
        $this->view->setTitle(($fullModelName::$objectName ? $fullModelName::$objectName : $fullModelName) . ($item ? (' - ' . $item->name()) : ''));
        if (!empty($_POST['comment'])) {
            $comment = new  \Inji\Dashboard\Comment();
            $comment->text = $_POST['comment'];
            $comment->user_id = \Users\User::$cur->id;
            $comment->model = $fullModelName;
            $comment->item_id = $item->pk();
            $comment->save();
            \Inji\Tools::redirect($_SERVER['REQUEST_URI']);
        }
        $viewOptions = !empty($fullModelName::$views['manager']) ? $fullModelName::$views['manager'] : [];
        $moduleName = $this->module->name;
        $pageParam = ['module' => 'Ui', 'content' => 'dataManager/view', 'data' => compact('item', 'moduleName', 'viewOptions')];
        if (isset($_GET['print'])) {
            $pageParam['page'] = 'print';
        }
        $this->view->page($pageParam);
    }
}