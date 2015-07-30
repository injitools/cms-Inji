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

namespace View;

class Template extends \Object {

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
     * @var string
     */
    public $config = '';

    /**
     * Current template page for rendering
     * 
     * @var string
     */
    public $page = 'index';

    /**
     * Current template page path for rendering
     *
     * @var string
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
     * @var string
     */
    public $contentPath = '';

    /**
     * Setup template object
     * 
     * @param array $params
     */
    function __construct($params = []) {
        $this->setParams($params);
        if (!$this->path) {
            $this->path = $this->app->view->templatesPath . '/' . $this->name;
        }
        $this->loadConfig();
        $this->setPage();
        $this->setModule();
        $this->setContent();
    }

    /**
     * Load template config
     * 
     * @param string $configPath
     */
    function loadConfig($configPath = '') {
        if (!$configPath) {
            $configPath = $this->path . '/config.php';
        }
        $this->configPath = $configPath;
        $this->config = \Config::custom($this->configPath);
    }

    /**
     * Set params for template
     * 
     * @param array $params
     */
    function setParams($params) {
        foreach ($params as $param => $value) {
            $this->$param = $value;
        }
    }

    /**
     * Set page and page path for template
     * 
     * @param string $page
     */
    function setPage($page = '') {
        if (!$page) {
            $page = !empty($this->config['defaultPage']) ? $this->config['defaultPage'] : $this->page;
        }
        $this->page = $page;
        if (!$this->pagePath) {
            $this->pagePath = $this->path . '/' . $this->page . '.html';
        }
        $this->pagePath = \Tools::pathsResolve($this->getPagePaths(), $this->pagePath);
    }

    /**
     * Get posible paths for template page by name
     * 
     * @param type $page
     * @return type
     */
    function getPagePaths($page = '') {
        if (!$page) {
            $page = $this->page;
        }
        return [
            'template' => $this->path . '/' . $page . '.html',
            'defaultPage' => \Module::getModulePath('View') . '/templatePages/' . $page . '.html'
        ];
    }

    /**
     * Set module for content path finder
     * 
     * @param \Module $module
     */
    function setModule($module = null) {
        if (!$module && !$this->module) {
            $this->module = \Module::$cur;
        } else {
            $this->module = $module;
        }
        if (is_string($this->module)) {
            $this->module = $this->app->{$this->module};
        }
    }

    /**
     * Set content file for rendering
     * 
     * @param string $content
     */
    function setContent($content = '') {
        if ($content) {
            $this->content = $content;
        }
        if (!$this->content) {
            $this->content = \Controller::$cur ? \Controller::$cur->method : '';
        }
        if (!$this->contentPath && \Module::$cur) {
            $this->contentPath = \Module::$cur->path . '/' . \Module::$cur->app->type . "Controllers/content/{$this->content}.php";
        }
        $this->contentPath = \Tools::pathsResolve($this->getContentPaths(), $this->contentPath);
    }

    /**
     * Return posible path for content file by name
     * 
     * @param string $content
     * @return string
     */
    function getContentPaths($content = '') {
        if (!$content) {
            $content = $this->content;
        }
        $paths = [];
        if ($this->module) {
            $paths['templateModule'] = $this->path . "/modules/{$this->module->moduleName}/{$content}.php";
        }
        if (\Module::$cur) {
            $paths['templateCurModule'] = $this->path . "/modules/".\Module::$cur->moduleName."/{$content}.php";
        }
        if (\Controller::$cur) {
            $paths['appControllerContent'] = \Controller::$cur->module->app->path . '/modules/' . \Controller::$cur->module->moduleName . '/' . \Controller::$cur->module->app->type . "Controllers/content/{$content}.php";
            $paths['controllerContent'] = \Controller::$cur->path . "/content/{$content}.php";
            $paths['moduleControllerContent'] = \Controller::$cur->module->path . '/' . \Controller::$cur->module->app->type . "Controllers/content/{$content}.php";
        }
        if ($this->module) {
            $paths['customModuleTemplateControllerContent'] = $this->path . "/modules/" . $this->module->moduleName . "/{$content}.php";
        }
        if ($this->module && \Controller::$cur) {
            $paths['customModuleControllerContent'] = $this->module->path . '/' . \Controller::$cur->module->app->type . "Controllers/content/{$content}.php";
        }
        return $paths;
    }

    /**
     * Retrn object of template by template name
     * 
     * @param string $templateName
     * @param \App $app
     * @return \View\Template
     */
    static function get($templateName, $app = null, $templatesPath = '') {
        if (!$app) {
            $app = \App::$cur;
        }
        if (!$templatesPath) {
            $templatesPath = $app->view->templatesPath;
        }
        if (file_exists($templatesPath . '/' . $templateName)) {
            return new Template([
                'name' => $templateName,
                'path' => $templatesPath . '/' . $templateName,
                'app' => $app
            ]);
        }
    }

}
