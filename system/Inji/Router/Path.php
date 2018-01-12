<?php

namespace Inji\Router;


class Path {
    public $path, $moduleName;

    public function __construct($path, $moduleName = '') {
        $this->path = $path;
        $this->moduleName = $moduleName;
    }
}