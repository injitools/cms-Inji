<?php

namespace Inji\View\Page;

use Inji\View\Page;

class Assets {
    /**
     * @var Page
     */
    public $page;

    function __construct(Page $page) {
        $this->page = $page;
    }
}