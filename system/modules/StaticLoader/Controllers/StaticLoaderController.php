<?php

/**
 * Item name
 *
 * Info
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class StaticLoaderController extends Controller {

    function indexAction() {
        $path = $this->module->parsePath(func_get_args());
        $this->module->giveFile(App::$cur->path . '/static/' . $path);
    }

    function templatesAction() {

        $path = $this->module->parsePath(func_get_args());
        $this->module->giveFile($this->view->templatesPath . '/' . $path);
    }

    function moduleAssetAction() {
        $params = func_get_args();
        if (empty($params[0])) {
            $this->module->header(404, true);
        }
        $module = App::$cur->$params[0];

        if (!$module) {
            $this->module->header(404, true);
        }

        $path = $module->path . '/static/' . $this->module->parsePath(array_slice($params, 1));
        $this->module->giveFile($path);
    }

    function systemAction() {
        $path = $this->module->parsePath(func_get_args());
        $this->module->giveFile(INJI_SYSTEM_DIR . '/static/' . $path);
    }

}