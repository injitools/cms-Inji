<?php
/**
 * Created by IntelliJ IDEA.
 * User: inji
 * Date: 08.01.2018
 * Time: 1:20
 */

namespace Inji\Router;


class Path {
    public $path, $moduleName;

    public function __construct($path, $moduleName = '') {
        $this->path = $path;
        $this->moduleName = $moduleName;
    }
}