<?php

namespace Inji\View\Page;

use Inji\App;
use Inji\View\Page;

class Head {
    /**
     * @var Page
     */
    public $page;

    function __construct(Page $page) {
        $this->page = $page;
    }

    public function generate() {
        $html = '';
        foreach ($this->elements() as $el) {
            $html .= call_user_func_array(['Inji\Html', 'el'], $el) . "\n";
        }
        return $html;
    }

    public function elements() {
        $els = [
            ['title', $this->page->title],
            ['link', '', ['rel' => 'shortcut icon', 'href' => $this->faviconPath()], null]
        ];
        foreach ($this->getMetaTags() as $meta) {
            $els[] = ['meta', $meta, '', null];
        }

        if (empty($this->page->template->config['noInjects']) && !empty(\Inji::$config['assets']['js'])) {
            foreach (\Inji::$config['assets']['js'] as $js) {
                $this->customAsset('js', $js);
            }
        }

        $this->checkNeedLibs();
        $this->parseCss();
        echo "\n        <script src='" . Statics::file("/static/system/js/Inji.js") . "'></script>";
        return $els;
    }

    public function customAsset($type, $asset, $lib = false) {
        if (!$lib) {
            $this->dynAssets[$type][] = $asset;
        } else {
            if (empty($this->libAssets[$type][$lib]) || !in_array($asset, $this->libAssets[$type][$lib])) {
                $this->libAssets[$type][$lib][] = $asset;
            }
        }
    }

    public function faviconPath() {
        if (!empty(App::$primary->config['site']['favicon']) && file_exists(App::$primary->path . '/' . App::$primary->config['site']['favicon'])) {
            return App::$primary->config['site']['favicon'];
        }
        if (!empty($this->page->template->config['favicon']) && file_exists($this->page->template->path . "/{$this->page->template->config['favicon']}")) {
            return $this->page->template->name . '/' . $this->page->template->config['favicon'];
        }
        if (!empty($this->page->template->config['favicon']) && file_exists($this->page->app->path . "/static/images/{$this->page->template->config['favicon']}")) {
            return '/static/images/' . $this->page->template->config['favicon'];
        }
        if (file_exists($this->page->app->path . '/static/images/favicon.ico')) {
            return '/static/images/favicon.ico';
        }
    }

    public function getMetaTags() {
        $metas = [];

        if (!empty($this->page->app->config['site']['keywords'])) {
            $metas['metaName:keywords'] = ['name' => 'keywords', 'content' => $this->page->app->config['site']['keywords']];
        }
        if (!empty($this->page->app->config['site']['description'])) {
            $metas['metaName:description'] = ['name' => 'description', 'content' => $this->page->app->config['site']['description']];
        }
        if (!empty($this->page->app->config['site']['metatags'])) {
            foreach ($this->page->app->config['site']['metatags'] as $meta) {
                if (!empty($meta['name'])) {
                    $metas['metaName:' . $meta['name']] = $meta;
                } elseif (!empty($meta['property'])) {
                    $metas['metaProperty:' . $meta['property']] = $meta;
                }
            }
        }
        if ($this->dynMetas) {
            $metas = array_merge($metas, $this->dynMetas);
        }
        return $metas;
    }
}