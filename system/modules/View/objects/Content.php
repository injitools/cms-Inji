<?php


namespace Inji\View;


class Content {
    public $name = '';
    public $path = '';
    public $data = [];
    /**
     * @var Page
     */
    public $page;

    function __construct($params, ?Page $page) {
        $this->name = $params['name'] ?? 'index';
        $this->setPath($params['path'] ?? false);
        $this->setData($params['data'] ?? []);
    }

    function setData($data = []) {
        if ($data) {
            $this->data = array_merge($this->data, $data);
        }
    }

    function setPath($path = false) {
        if ($path) {
            $this->path = $path;
        } elseif (!$this->path) {
            $this->path = \Inji\Tools::pathsResolve($this->getPaths($this->name), $this->path);
        }

    }

    public function getPaths($content = '') {
        if (!$content) {
            $content = $this->name;
        }
        $paths = [];
        if ($this->page && $this->page->module) {
            if (\Inji\Controller::$cur) {
                $paths['templateModuleController'] = $this->path . "/modules/{$this->page->module->name}/" . \Inji\Controller::$cur->name . "/{$content}.php";
            }
            $paths['templateModule'] = $this->path . "/modules/{$this->page->module->name}/{$content}.php";
        }
        if (\Inji\Module::$cur) {
            if (\Inji\Controller::$cur) {
                $paths['templateCurModuleController'] = $this->path . "/modules/" . \Inji\Module::$cur->name . "/" . \Inji\Controller::$cur->name . "/{$content}.php";
            }
            $paths['templateCurModule'] = $this->path . "/modules/" . \Inji\Module::$cur->name . "/{$content}.php";
        }
        if (\Inji\Controller::$cur) {
            $modulePaths = \Inji\Module::getModulePaths(\Inji\Controller::$cur->module->name);
            foreach ($modulePaths as $key => $modulePath) {
                $paths['module_' . $key . '_appType'] = $modulePath . '/views/' . \Inji\Controller::$cur->module->app->type . '/' . \Inji\Controller::$cur->name . "/{$content}.php";
                $paths['module_' . $key . '_appType_controllerName'] = $modulePath . '/views/' . \Inji\Controller::$cur->module->app->type . "/{$content}.php";
                $paths['module_' . $key] = $modulePath . '/views/' . "/{$content}.php";
            }
        }

        if ($this->page->module) {
            if (\Inji\Controller::$cur) {
                $paths['customModuleTemplateControllerContentController'] = $this->path . "/modules/" . $this->page->module->name . "/" . \Inji\Controller::$cur->name . "/{$content}.php";
            }
            $paths['customModuleTemplateControllerContent'] = $this->path . "/modules/" . $this->page->module->name . "/{$content}.php";
        }
        if ($this->page->module && \Inji\Controller::$cur) {
            $paths['customModuleControllerContentController'] = $this->page->module->path . '/' . \Inji\Controller::$cur->module->app->type . "Controllers/content/" . \Inji\Controller::$cur->name . "/{$content}.php";
            $paths['customModuleControllerContent'] = $this->page->module->path . '/' . \Inji\Controller::$cur->module->app->type . "Controllers/content/{$content}.php";
        }
        return $paths;
    }
}