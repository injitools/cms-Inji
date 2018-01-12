<?php

/**
 * Template
 *
 * Object for template
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\View;

use Inji\Config;

class Template extends \Inji\InjiObject {

    /**
     * App for template
     *
     * @var \App
     */
    public $app = null;

    /**
     * Template name
     *
     * @var string
     */
    public $name = 'default';

    /**
     * Template path
     *
     * @var string
     */
    public $path = '';

    /**
     * Template config path
     *
     * @var string
     */
    public $configPath = '';

    /**
     * Template config
     *
     * @var array
     */
    public $config = [];

    /**
     * Current template page for rendering
     *
     * @var string
     */
    public $page = 'index';

    /**
     * Current template page path for rendering
     *
     * @var string|boolean
     */
    public $pagePath = '';

    /**
     * Template module for content path finder
     *
     * @var \Module
     */
    public $module = null;

    /**
     * Current content file for rendering
     *
     * @var string
     */
    public $content = '';

    /**
     * Current content file path for rendering
     *
     * @var string|boolean
     */
    public $contentPath = '';

    /**
     * Setup template object
     *
     * @param array $params
     */
    public function __construct($params = []) {
        $this->setParams($params);
        if (!$this->path) {
            $this->path = $this->app->view->templatesPath() . '/' . $this->name;
        }
        $this->loadConfig();
    }

    /**
     * Load template config
     *
     * @param string $configPath
     */
    public function loadConfig($configPath = '') {
        if (!$configPath) {
            $configPath = $this->path . '/config.php';
        }
        $this->configPath = $configPath;
        $this->config = Config::custom($this->configPath);
    }

    /**
     * Set params for template
     *
     * @param array $params
     */
    public function setParams($params) {
        foreach ($params as $param => $value) {
            $this->$param = $value;
        }
    }

    /**
     * Set page and page path for template
     *
     * @param string $page
     */
    public function setPage($page = '') {
        if (!$page) {
            $page = !empty($this->config['defaultPage']) ? $this->config['defaultPage'] : $this->page;
        }
        $this->page = $page;
        if (!$this->pagePath) {
            $this->pagePath = $this->path . '/' . $this->page . '.html';
        }
        $this->pagePath = \Inji\Tools::pathsResolve($this->getPagePaths(), $this->pagePath);
    }

    /**
     * Set content file for rendering
     *
     * @param string $content
     */
    public function setContent($content = '') {
        if ($content) {
            $this->content = $content;
        }
        if (\Inji\Controller::$cur && \Inji\Controller::$cur->run) {
            if (!$this->content) {
                $this->content = \Inji\Controller::$cur->method;
            }
            if ((!$this->contentPath || $content) && \Inji\Module::$cur) {
                $this->contentPath = \Inji\Module::$cur->path . '/' . \Inji\Module::$cur->app->type . "Controllers/content/{$this->content}.php";
            }
            $this->contentPath = \Inji\Tools::pathsResolve($this->getContentPaths(), $this->contentPath);
        }
    }

    /**
     * Return posible path for content file by name
     *
     * @param string $content
     * @return string
     */
    public function getContentPaths($content = '') {
        if (!$content) {
            $content = $this->content;
        }
        $paths = [];
        if ($this->module) {
            if (\Inji\Controller::$cur) {
                $paths['templateModuleController'] = $this->path . "/modules/{$this->module->name}/" . \Inji\Controller::$cur->name . "/{$content}.php";
            }
            $paths['templateModule'] = $this->path . "/modules/{$this->module->name}/{$content}.php";
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

        if ($this->module) {
            if (\Inji\Controller::$cur) {
                $paths['customModuleTemplateControllerContentController'] = $this->path . "/modules/" . $this->module->name . "/" . \Inji\Controller::$cur->name . "/{$content}.php";
            }
            $paths['customModuleTemplateControllerContent'] = $this->path . "/modules/" . $this->module->name . "/{$content}.php";
        }
        if ($this->module && \Inji\Controller::$cur) {
            $paths['customModuleControllerContentController'] = $this->module->path . '/' . \Inji\Controller::$cur->module->app->type . "Controllers/content/" . \Inji\Controller::$cur->name . "/{$content}.php";
            $paths['customModuleControllerContent'] = $this->module->path . '/' . \Inji\Controller::$cur->module->app->type . "Controllers/content/{$content}.php";
        }
        return $paths;
    }

    /**
     * Retrn object of template by template name
     *
     * @param string $templateName
     * @param \Inji\App $app
     * @return \Inji\View\Template
     */
    public static function get($templateName, $app = null, $templatesPath = '') {
        if (!$app) {
            $app = \Inji\App::$cur;
        }
        if (!$templatesPath) {
            $templatesPath = $app->view->templatesPath();
        }
        return new Template([
            'name' => $templateName,
            'path' => $templatesPath . '/' . $templateName,
            'app' => $app
        ]);
    }

}
