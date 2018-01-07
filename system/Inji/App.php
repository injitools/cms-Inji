<?php

namespace Inji;
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
    public $namespace = '';
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
        $possibleModules = $this->possibleModuleClasses($moduleName);
        foreach ($possibleModules as $possibleModule) {
            if (class_exists($possibleModule)) {
                return $possibleModule;
            }
        }
        if (!empty($this->config['moduleRouter'])) {
            foreach ($this->config['moduleRouter'] as $route => $module) {
                if (preg_match("!{$route}!i", $moduleName)) {
                    $possibleModules = $this->possibleModuleClasses($module);
                    foreach ($possibleModules as $possibleModule) {
                        if (class_exists($possibleModule)) {
                            return $possibleModule;
                        }
                    }
                }
            }
        }
        if (!empty(\Inji::$config['moduleRouter'])) {
            foreach (\Inji::$config['moduleRouter'] as $route => $module) {
                if (preg_match("!{$route}!i", $moduleName)) {
                    $possibleModules = $this->possibleModuleClasses($module);
                    foreach ($possibleModules as $possibleModule) {
                        if (class_exists($possibleModule)) {
                            return $possibleModule;
                        }
                    }
                }
            }
        }
        return false;
    }
    public function possibleModuleClasses($moduleName){
        $possibleModules = [];
        if ($this->namespace) {
            $possibleModules[] = $this->namespace . '\\' . $moduleName;
        }
        $possibleModules[] = "Inji\\{$moduleName}";
        return $possibleModules;
    }

    public function isLoaded($moduleName) {
        return !empty($this->_objects[$moduleName]);
    }

    public function getDomain($decode = false) {
        return !empty($this->config['site']['domain']) ? $this->config['site']['domain'] : ($decode ? idn_to_utf8(INJI_DOMAIN_NAME) : INJI_DOMAIN_NAME);
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
        if ($moduleClassName === false) {
            return false;
        }
        $this->_objects[$className][$paramsStr] = new $moduleClassName($this);
        if (isset($this->_objects[$className][$paramsStr])) {
            $this->_objects[$className][$paramsStr]->checkDbMigration();
            if (method_exists($this->_objects[$className][$paramsStr], 'init')) {
                call_user_func_array([$this->_objects[$className][$paramsStr], 'init'], $params);
            }
            return $this->_objects[$className][$paramsStr];
        }
        return false;
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
     * @return Module|null
     */
    public function __call($className, $params) {
        return $this->getObject($className, $params);
    }
}