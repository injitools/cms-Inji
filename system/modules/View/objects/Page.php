<?php

namespace Inji\View;

use Inji\App;
use Inji\Controller;
use Inji\Module;
use Inji\Tools;
use Inji\View\Page\Assets;
use Inji\View\Page\Head;

class Page {
    /**
     * @var Template
     */
    public $template;
    /**
     * @var App
     */
    public $app;
    /**
     * @var Module
     */
    public $module;
    /**
     * @var Head
     */
    public $head;
    /**
     * @var Assets
     */
    public $assets;
    /**
     * @var Content
     */
    public $content;
    public $name = '';
    public $path = '';
    public $title = '';


    function __construct($params, ?App $app = null) {
        $this->app = $app ?? App::$cur;
        $this->setParams($params);
        $this->head = new Head($this);
        $this->assets = new Assets($this);
        if (empty($this->template->config['noInjects']) && !empty(\Inji::$config['assets']['js'])) {
            foreach (\Inji::$config['assets']['js'] as $js) {
                $this->customAsset('js', $js);
            }
        }
    }

    public function setTitle($title, $add = true) {
        if ($add && !empty($this->app->config['site']['name'])) {
            if ($title) {
                $this->title = $title . ' - ' . $this->app->config['site']['name'];
            } else {
                $this->title = $this->app->config['site']['name'];
            }
        } else {
            $this->title = $title;
        }
    }

    function setParams($params) {

        $this->setTemplate($params['template'] ?? 'default');

        $this->setPage($params['page'] ?? false);

        $this->setPath($params['pagePath'] ?? false);

        $this->setModule($params['module'] ?? false);

        $this->setContent([
            'name' => $params['content'] ?? false,
            'path' => $params['contentPath'] ?? false,
            'data' => $params['data'] ?? [],
        ]);
    }

    public function setContent($content = false) {
        if ($content instanceof Content) {
            $this->content = $content;
        }
        if (!$this->content) {
            if ($content && is_string($content)) {
                $this->content = new Content(['name' => $content], $this);
            } elseif ($content && is_array($content)) {
                $this->content = new Content($content, $this);
            } elseif (Controller::$cur && Controller::$cur->run) {
                $this->content = new Content(['name' => Controller::$cur->method], $this);
            }
        }
    }

    /**
     * Set module for content path finder
     *
     * @param \Inji\Module $module
     */
    public function setModule($module = null) {
        if (!$module && !$this->module) {
            $this->module = \Inji\Module::$cur;
        } else {
            $this->module = $module;
        }
        if (is_string($this->module)) {
            $this->module = $this->app->{$this->module};
        }
    }

    public function setPath($path = '') {
        if ($path) {
            $this->path = $path;
        } elseif (!$this->path) {
            $this->path = Tools::pathsResolve($this->possiblePaths($this->name), false);
        }
        if (!file_exists($this->path)) {
            throw new \Exception('Page path not exist');
        }
    }

    public function setPage($pageName = '') {
        if ($pageName && $pageName != 'current') {
            $this->name = $pageName;
        } elseif (!$this->name && !empty($this->app->view->config['defaultPage'])) {
            $this->name = $this->app->view->config['defaultPage'];
        } else {
            $this->name = 'index';
        }
        return $this->name;
    }

    public function setTemplate($template) {
        if ($template instanceof Template) {
            $this->template = $template;
        } elseif ($template && is_string($template) && $template != 'current') {
            $this->template = Template::get($template, $this->app, $this->app->view->templatesPath());
        } else {
            $this->template = Template::get('default', $this->app, $this->app->view->templatesPath());
        }
        return $this->template;
    }

    function possiblePaths($page) {
        $paths = [
            'template' => $this->template->path . '/' . $page . '.html'
        ];
        foreach (\Inji\Module::getModulePaths('View') as $pathName => $path) {
            $paths[$pathName] = $path . '/templatePages/' . $page . '.html';
        }
        return $paths;
    }

    function send() {
        App::$cur->log->template_parsed = true;
        if (file_exists($this->path)) {
            $parser = new \Inji\View\Parser\Page($this);
            echo $parser->parse();
        } else {
            $this->content->draw();
        }
    }
}