<?php

namespace Inji;
/**
 * Module
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class Module {

    /**
     * Storage of cur requested module
     *
     * @var Module
     */
    public static $cur = null;

    /**
     * Module name
     *
     * @var string
     */
    public $name = '';

    /**
     * Module config
     *
     * @var array
     */
    public $config = [];

    /**
     * Module info
     *
     * @var array
     */
    public $info = [];

    /**
     * Requested module params
     *
     * @var array
     */
    public $params = [];

    /**
     * Module directory path
     *
     * @var string
     */
    public $path = '';

    /**
     * Module app
     *
     * @var App
     */
    public $app = null;

    /**
     * Parse cur module
     *
     * @param App $app
     */
    public function __construct($app) {
        $this->app = $app;
        if (!$this->name) {
            $this->name = get_class($this);
        }
        $this->path = Router::getLoadedClassPath(get_class($this));
        $this->info = $this->getInfo();
        $this->config = Config::module($this->name, !empty($this->info['systemConfig']));
        $that = $this;
        \Inji::$inst->listen('Config-change-module-' . $this->app->name . '-' . $this->name, $this->app->name . '-' . $this->name . 'config', function ($event) use ($that) {
            $that->config = $event['eventObject'];
            return $event['eventObject'];
        });
    }

    /**
     * Get all posible directorys for module files
     *
     * @param string $moduleName
     * @return array
     */
    public static function getModulePaths($moduleName) {
        $moduleName = ucfirst($moduleName);
        $paths = [];
        if (App::$cur !== App::$primary) {
            $paths['primaryAppPath'] = App::$primary->path . '/modules/' . $moduleName;
        }
        $paths['curAppPath'] = App::$cur->path . '/modules/' . $moduleName;
        $paths['systemPath'] = INJI_SYSTEM_DIR . '/modules/' . $moduleName;
        return $paths;
    }

    /**
     * Return directory where places module file
     *
     * @param string $moduleName
     * @return string
     */
    public static function getModulePath($moduleName) {
        $moduleName = ucfirst($moduleName);
        $paths = Module::getModulePaths($moduleName);
        foreach ($paths as $path) {
            if (file_exists($path . '/' . $moduleName . '.php')) {
                return $path;
            }
        }
    }

    /**
     * Check module for installed
     *
     * @param string $moduleName
     * @param \Inji\App $app
     * @return boolean
     */
    public static function installed($moduleName, $app) {
        if (in_array($moduleName, self::getInstalled($app))) {
            return true;
        }
        return false;
    }

    /**
     * Get installed modules for app
     *
     * @param \Inji\App $app
     * @param App $primary
     * @return array
     */
    public static function getInstalled($app, $primary = false) {
        if (!$primary) {
            $primary = \Inji\App::$primary;
        }
        $system = !empty(\Inji::$config['modules']) ? \Inji::$config['modules'] : [];
        $primary = !empty($primary->config['modules']) ? $primary->config['modules'] : [];
        $actual = $app !== $primary && !empty($app->config['modules']) ? $app->config['modules'] : [];
        $modules = array_unique(array_merge($system, $primary, $actual));
        return $modules;
    }

    /**
     * Find module controllers
     *
     * @param string $moduleName
     * @return array
     */
    public static function getModuleControllers($moduleName) {
        $controllers = [];
        $moduleDirs = static::getModulePaths($moduleName);
        foreach ($moduleDirs as $moduleDir) {
            if (is_dir($moduleDir)) {
                foreach (scandir($moduleDir) as $dir) {
                    if (preg_match('!Controllers$!', $dir) && is_dir($moduleDir . '/' . $dir)) {
                        $path = $moduleDir . '/' . $dir;
                        foreach (scandir($path) as $file) {
                            if (preg_match('!Controller\.php$!', $file) && is_file($path . '/' . $file)) {
                                $controllerName = preg_replace('!Controller\.php$!', '', $file);
                                $controllers[preg_replace('!Controllers$!', '', $dir)][$controllerName] = $path . '/' . $file;
                            }
                        }
                    }
                }
            }
        }
        return $controllers;
    }

    /**
     * Find module by request
     *
     * @param \Inji\App $app
     * @param array|null $params
     * @return \Inji\Module
     */
    public static function resolveModule($app, $params = null) {
        $search = is_array($params) ? $params : $app->params;
        if (!empty($search[0]) && $app->{$search[0]}) {
            $module = $app->{$search[0]};
            $module->params = array_slice($search, 1);
            return $module;
        }
        if (!empty($app->config['defaultModule']) && $app->{$app->config['defaultModule']}) {
            $module = $app->{$app->config['defaultModule']};
            $module->params = $app->params;
            return $module;
        }

        if ($app->Main) {
            $module = $app->Main;
            $module->params = $app->params;
            return $module;
        }
        return null;
    }

    /**
     * Get posible path for controller
     *
     * @return array
     */
    public function getPossibleControllers() {
        $possibleClasses = [];
        if (!empty($this->params[0]) && ucfirst($this->params[0]) != $this->name) {
            $possibleClasses['curApp_slice'] = $this->app->namespace . '\\' . $this->name . '\\' . ucfirst($this->params[0]) . ucfirst($this->app->type) . 'Controller';
            $possibleClasses['system_slice'] = 'Inji\\' . $this->name . '\\' . ucfirst($this->params[0]) . ucfirst($this->app->type) . 'Controller';
            $possibleClasses['universal_curApp_slice'] = $this->app->namespace . '\\' . $this->name . '\\' . ucfirst($this->params[0]) . 'Controller';
            $possibleClasses['universal_system_slice'] = 'Inji\\' . $this->name . '\\' . ucfirst($this->params[0]) . 'Controller';
        }
        $possibleClasses['curApp'] = $this->app->namespace . '\\' . $this->name . '\\' . $this->name . ucfirst($this->app->type) . 'Controller';
        $possibleClasses['system'] = 'Inji\\' . $this->name . '\\' . $this->name . ucfirst($this->app->type) . 'Controller';

        $possibleClasses['universal_curApp'] = $this->app->namespace . '\\' . $this->name . '\\' . $this->name . 'Controller';
        $possibleClasses['universal_system'] = 'Inji\\' . $this->name . '\\' . $this->name . 'Controller';
        return $possibleClasses;
    }

    /**
     * Find controller by request
     *
     * @return \Inji\Controller
     */
    public function findController() {
        $possibleClasses = $this->getPossibleControllers();
        foreach ($possibleClasses as $possibleClassType => $possibleClass) {
            if (class_exists($possibleClass)) {
                if (strpos($possibleClassType, 'slice')) {
                    $controllerName = ucfirst($this->params[0]);
                    $params = array_slice($this->params, 1);
                } else {
                    $controllerName = $this->name;
                    $params = $this->params;
                }
                $controller = new $possibleClass();
                $controller->params = $params;
                $controller->module = $this;
                $controller->path = Router::getLoadedClassPath($possibleClass);
                $controller->name = $controllerName;
                return $controller;
            }
        }
    }

    /**
     * Return module info
     *
     * @param string $moduleName
     * @return array
     */
    public static function getInfo($moduleName = '') {
        if (!$moduleName && get_called_class()) {
            $moduleName = get_called_class();
        } elseif (!$moduleName) {
            return [];
        }
        $paths = Module::getModulePaths($moduleName);
        foreach ($paths as $path) {
            if (file_exists($path . '/info.php')) {
                return include $path . '/info.php';
            }
        }
        return [];
    }

    /**
     * Return snippets by name
     *
     * @param string $snippetsPath
     * @param boolean $extensions
     * @param string $dir
     * @param string $moduleName
     * @return array
     */
    public function getSnippets($snippetsPath, $extensions = true, $dir = '/snippets', $moduleName = '') {
        $moduleName = $moduleName ? $moduleName : $this->name;
        $modulePaths = Module::getModulePaths($moduleName);
        $modulePaths = array_reverse($modulePaths);
        $modulePaths['templatePath'] = App::$cur->view->template->path . '/modules/' . ucfirst($moduleName);
        $snippets = [];
        foreach ($modulePaths as $path) {
            if (file_exists($path . $dir . '/' . $snippetsPath)) {
                $snippetsPaths = array_slice(scandir($path . $dir . '/' . $snippetsPath), 2);
                foreach ($snippetsPaths as $snippetPath) {
                    if (is_dir($path . $dir . '/' . $snippetsPath . '/' . $snippetPath)) {
                        $snippets[$snippetPath] = include $path . $dir . '/' . $snippetsPath . '/' . $snippetPath . '/info.php';
                    } else {
                        $snippets[pathinfo($snippetPath, PATHINFO_FILENAME)] = include $path . $dir . '/' . $snippetsPath . '/' . $snippetPath;
                    }
                }
            }
        }
        if ($extensions) {
            $snippets = array_merge($snippets, $this->getExtensions('snippets', $snippetsPath));
        }
        return $snippets;
    }

    /**
     * Return module objects
     *
     * @return array
     */
    public function getObjects($filterNamespace = '') {
        $moduleName = $this->name;
        $modulePaths = Module::getModulePaths($moduleName);
        $modulePaths = array_reverse($modulePaths);
        $scanFn = function ($path, $namespace, &$files = []) use (&$scanFn, $filterNamespace) {
            if (file_exists($path)) {
                foreach (scandir($path) as $item) {
                    if (in_array($item, ['..', '.'])) {
                        continue;
                    }
                    $filename = pathinfo($item)['filename'];
                    if (is_dir($path . '/' . $item)) {
                        $scanFn($path . '/' . $item, $namespace . '\\' . $filename, $files);
                    } else {
                        if (!$filterNamespace || strpos($namespace, $filterNamespace) === 0) {
                            $files[$path . '/' . $item] = $namespace . '\\' . $filename;
                        }
                    }
                }
            }
            return $files;
        };
        $files = [];
        foreach ($modulePaths as $path) {
            $scanFn($path . '/objects', $moduleName, $files);
        }
        return $files;
    }

    /**
     * Return module models
     *
     * @return array
     */
    public static function getModels($moduleName, $filterNamespace = '') {
        $modulePaths = Module::getModulePaths($moduleName);
        $modulePaths = array_reverse($modulePaths);
        $scanFn = function ($path, $namespace, &$files = []) use (&$scanFn, $filterNamespace) {
            if (file_exists($path)) {
                foreach (scandir($path) as $item) {
                    if (in_array($item, ['..', '.'])) {
                        continue;
                    }
                    $filename = pathinfo($item)['filename'];
                    if (is_dir($path . '/' . $item)) {
                        $scanFn($path . '/' . $item, $namespace . '\\' . $filename, $files);
                    } else {
                        if (!$filterNamespace || strpos($namespace, $filterNamespace) === 0) {
                            $files[$path . '/' . $item] = $namespace . '\\' . $filename;
                        }
                    }
                }
            }
            return $files;
        };
        $files = [];
        foreach ($modulePaths as $path) {
            $scanFn($path . '/models', $moduleName, $files);
        }
        return $files;
    }

    /**
     * Return extensions for type
     *
     * @param string $extensionType
     * @param string $request
     * @return array
     */
    public function getExtensions($extensionType, $request) {
        $extensions = [];
        $modules = Module::getInstalled(App::$cur);
        $method = 'get' . ucfirst($extensionType);
        foreach ($modules as $module) {
            $extensions = array_merge($extensions, $this->{$method}($request, false, "/extensions/{$this->name}/" . $extensionType, $module));
        }
        return $extensions;
    }

    public function checkDbMigration() {
        if (empty($this->info['migrations'])) {
            return true;
        }
        $code = 'module:' . get_called_class();
        $newMigrations = App::$cur->db->compareMigrations($code, $this->info['migrations']);
        foreach ($newMigrations as $version => $migrationOption) {
            $migration = include $this->path . '/migrations/' . $migrationOption . '.php';
            App::$cur->db->makeMigration($code, $version, $migration);
        }
    }

    public function sitemap() {
        return [];
    }
}