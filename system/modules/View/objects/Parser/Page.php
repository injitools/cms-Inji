<?php

namespace Inji\View\Parser;
class Page extends \Inji\View\Parser {
    /**
     * @var \Inji\View\Page
     */
    public $page;

    function __construct(\Inji\View\Page $page) {
        $this->page = $page;
    }

    function parse() {
        $html = '';
        if (file_exists($this->page->path)) {
            $source = file_get_contents($this->page->path);
            if (strpos($source, 'BODYEND') === false) {
                $source = str_replace('</body>', '{BODYEND}</body>', $source);
            }
            $html = $this->parseSource($source);
        }
        return $html;
    }

    function contentTag() {
        return $this->page->content->render();
    }

    function titleTag() {
        return $this->page->title;
    }

    function widgetTag($widgetName, $params) {
        return $this->page->app->view->widget($widgetName, ['params' => $params], ':' . implode(':', $params));
    }

    function headTag() {
        return $this->page->head->generate();
    }

    function pageTag($pageName) {
        return $this->page->app->view->page(['page' => $pageName])->render();
    }

    function bodyendTag() {
        return $this->page->bodyEnd();
    }

    function template_pathTag() {
        return '/static/templates/' . $this->page->template->name;
    }
}