<?php

/**
 * App admin controller
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class adminController extends Controller {

  public function indexAction() {
    $args = func_get_args();
    call_user_func_array([$this, 'dataManagerAction'], $args);
  }

  public function dataManagerAction($model = '', $dataManager = 'manager') {
    if (!$model) {
      $modulePath = Module::getModulePath($this->module->moduleName);
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
    $fullModelName = $this->module->moduleName . '\\' . ucfirst($model);
    $dataManager = new Ui\DataManager($fullModelName, $dataManager);
    $title = !empty($dataManager->managerOptions['name']) ? $dataManager->managerOptions['name'] : $fullModelName::objectName();
    $this->view->setTitle($title);
    $this->view->page(['module' => 'Ui', 'content' => 'dataManager/manager', 'data' => compact('dataManager')]);
  }

  public function viewAction($model, $pk) {
    $fullModelName = $this->module->moduleName . '\\' . ucfirst($model);
    if (Users\User::$cur->group_id != 3 && (empty($fullModelName::$views['manager']['access']) || !in_array(Users\User::$cur->group_id, $fullModelName::$views['manager']['access']['groups']))) {
      Tools::redirect('/admin', 'У вас нет прав доступа для просмотра этого объекта', 'danger');
    }
    $item = $fullModelName::get($pk);
    $this->view->setTitle(($fullModelName::$objectName ? $fullModelName::$objectName : $fullModelName) . ($item ? ( ' - ' . $item->name()) : ''));
    if (!empty($_POST['comment'])) {
      $comment = new Dashboard\Comment();
      $comment->text = $_POST['comment'];
      $comment->user_id = \Users\User::$cur->id;
      $comment->model = $fullModelName;
      $comment->item_id = $item->pk();
      $comment->save();
      Tools::redirect($_SERVER['REQUEST_URI']);
    }
    $moduleName = $this->module->moduleName;
    $pageParam = ['module' => 'Ui', 'content' => 'dataManager/view', 'data' => compact('item', 'moduleName')];
    if (isset($_GET['print'])) {
      $pageParam['page'] = 'print';
    }
    $this->view->page($pageParam);
  }

}
