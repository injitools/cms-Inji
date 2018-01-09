<?php

namespace Inji;
/**
 * Ui module
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class Ui extends Module {
    public $name = 'Ui';

    public function init() {
        $this->app->view->customAsset('js', ['file' => '/static/moduleAsset/Ui/js/Ui.js']);
        $this->app->view->customAsset('js', ['file' => '/static/moduleAsset/Ui/js/DataManager.js']);
        if ((!$this->app->users || \Inji\Users\User::$cur->isAdmin()) && $this->app->type == 'app') {
            $this->app->view->customAsset('js', ['file' => '/static/moduleAsset/Ui/js/fastEdit.js', 'name' => 'Ui', 'libs' => ['ckeditor']]);
        }
        $this->app->view->customAsset('css', '/static/moduleAsset/Ui/css/Ui.css');
    }

    public function getModelForm($modelName, $formName = '') {
        if (!$formName) {
            $formName = 'manager';
        }
        return class_exists($modelName) && !empty($modelName::$forms[$formName]) ? $modelName::$forms[$formName] : [];
    }

}
