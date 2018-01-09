<?php

namespace Inji\Router;
class Folder {
    public $folder, $prefix, $priority, $moduleIndex, $moduleDirs;

    public function __construct($folder, $prefix, $priority, $moduleIndex, $moduleDirs) {
        $this->folder = $folder;
        $this->prefix = $prefix;
        $this->priority = $priority;
        $this->moduleIndex = $moduleIndex;
        $this->moduleDirs = $moduleDirs;
    }

    /**
     * @param $className
     * @return Path[]
     */
    public function forClass($className) {

        $paths = $this->moduleDirs($className);
        $paths += $this->prefix($className);
        $paths += $this->full($className);

        return $paths;
    }

    public function full($className) {
        if ($this->prefix !== '*') {
            return [];
        }
        $paths = [];
        $classPath = str_replace('/', '\\', $className);

        $paths['folderPath'] = new Path($this->folder . $classPath . '.php');
        $paths['folderPathDir'] = new Path($this->folder . $classPath . substr($className, strrpos($className, '\\')) . '.php');

        return $paths;
    }

    public function prefix($className) {
        if (strpos($className, $this->prefix) !== 0) {
            return [];
        }

        $paths = [];
        $cuttedPath = str_replace('\\', '/', substr($className, strlen($this->prefix)));

        $paths['folderPrefixPath'] = new Path($this->folder . $cuttedPath . '.php');
        $paths['folderPrefixPathDir'] = new Path($this->folder . $cuttedPath . '/' . substr($className, strrpos($className, '\\') + 1) . '.php');
        return $paths;
    }

    public function moduleDirs($className) {
        if ($this->moduleIndex === false || $this->moduleIndex >= substr_count($className, '\\')) {
            return [];
        }
        $paths = [];
        $classPathItems = explode('\\', $className);
        $moduleName = $classPathItems[$this->moduleIndex];
        $classPathStart = implode('/', array_slice($classPathItems, 0, $this->moduleIndex + 1));

        if (strpos($classPathStart, str_replace('\\', '/', $this->prefix)) === 0) {
            $classPathStart = substr($classPathStart, strlen($this->prefix));
        }

        $classPathEnd = implode('/', array_slice($classPathItems, $this->moduleIndex + 1));

        foreach ($this->moduleDirs as $moduleDir) {
            $dirPath = implode('/', [rtrim($this->folder, '/'), $classPathStart, $moduleDir, $classPathEnd]);
            $paths['folderModuleDirPath_' . $moduleDir] = new Path($dirPath . '.php', $moduleName);
            $paths['folderModuleDirPathDir_' . $moduleDir] = new Path($dirPath . substr($dirPath, strrpos($dirPath, '/')) . '.php', $moduleName);
        }
        return $paths;
    }
}