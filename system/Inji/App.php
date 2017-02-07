<?php

/**
 * App
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class App {

    /**
     * static instance
     *
     * @var App
     */
    public static $cur = null;

    /**
     * @var App
     */
    public static $primary = null;
    private $_objects = [];

    /**
     * App params
     */
    public $name = '';
    public $dir = '';
    public $type = 'app';
    public $system = false;
    public $default = false;
    public $route = '';
    public $installed = false;
    public $staticPath = '/static';
    public $templatesPath = '/static/templates';
    public $path = '';
    public $params = [];
    public $config = [];

    /**
     * Constructor App
     *
     * @param array $preSet
     */
    public function __construct($preSet = []) {
        foreach ($preSet as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * Return module object by name or alias
     *
     * @param string $className
     * @return Module
     */
    public function getObject($className, $params = []) {
        $paramsStr = serialize($params);
        $className = ucfirst($className);
        if (isset($this->_objects[$className][$paramsStr])) {
            return $this->_objects[$className][$paramsStr];
        }
        return $this->loadObject($className, $params);
    }

    /**
     * Find module class from each paths
     *
     * @param string $moduleName
     * @return mixed
     */
    public function findModuleClass($moduleName) {
        $paths = Module::getModulePaths($moduleName);
        foreach ($paths as $path) {
            if (file_exists($path . '/' . $moduleName . '.php')) {
                include_once $path . '/' . $moduleName . '.php';
                return $moduleName;
            }
        }
        if (!empty($this->config['moduleRouter'])) {
            foreach ($this->config['moduleRouter'] as $route => $module) {
                if (preg_match("!{$route}!i", $moduleName)) {
                    return $module;
                }
            }
        }
        if (!empty(Inji::$config['moduleRouter'])) {
            foreach (Inji::$config['moduleRouter'] as $route => $module) {
                if (preg_match("!{$route}!i", $moduleName)) {
                    return $module;
                }
            }
        }
        return false;
    }

    public function isLoaded($moduleName) {
        return !empty($this->_objects[$moduleName]);
    }

    /**
     * Load module by name or alias
     *
     * @param string $className
     * @return mixed
     */
    public function loadObject($className, $params = []) {
        $paramsStr = serialize($params);
        $moduleClassName = $this->findModuleClass($className);
        if (!is_bool($moduleClassName) && $moduleClassName != $className) {
            return $this->_objects[$moduleClassName][$paramsStr] = $this->_objects[$className][$paramsStr] = $this->getObject($moduleClassName);
        } elseif (Module::installed($className, $this) && class_exists($className)) {
            $this->_objects[$className][$paramsStr] = new $className($this);
        }
        if (isset($this->_objects[$className][$paramsStr])) {
            $this->_objects[$className][$paramsStr]->checkDbMigration();
            if (method_exists($this->_objects[$className][$paramsStr], 'init')) {
                call_user_func_array([$this->_objects[$className][$paramsStr], 'init'], $params);
            }
            return $this->_objects[$className][$paramsStr];
        }
        return null;
    }

    /**
     * Reference to module getter
     *
     * @param string $className
     * @return Module|null
     */
    public function __get($className) {
        return $this->getObject($className);
    }

    /**
     * Reference to module getter with params
     *
     * @param string $className
     * @param array $params
     * @return object|null
     */
    public function __call($className, $params) {
        return $this->getObject($className, $params);
    }
}