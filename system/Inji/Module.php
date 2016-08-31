<?php

/**
 * Module
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class Module
{
    /**
     * Storage of cur requested module
     * 
     * @var \Module
     */
    public static $cur = null;

    /**
     * Module name
     * 
     * @var string
     */
    public $moduleName = '';

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
     * @var \App
     */
    public $app = null;

    /**
     * Parse cur module
     * 
     * @param \App $app
     */
    public function __construct($app)
    {
        $this->app = $app;
        $this->moduleName = get_class($this);
        $this->path = Router::getLoadedClassPath($this->moduleName);
        $this->info = $this->getInfo();
        $this->config = Config::module($this->moduleName, !empty($this->info['systemConfig']));
        $that = $this;
        Inji::$inst->listen('Config-change-module-' . $this->app->name . '-' . $this->moduleName, $this->app->name . '-' . $this->moduleName . 'config', function($event) use ($that) {
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
    public static function getModulePaths($moduleName)
    {
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
    public static function getModulePath($moduleName)
    {
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
     * @param \App $app
     * @return boolean
     */
    public static function installed($moduleName, $app)
    {
        if (in_array($moduleName, self::getInstalled($app))) {
            return true;
        }
        return FALSE;
    }

    /**
     * Get installed modules for app
     * 
     * @param \App $app
     * @param boolean|\App $primary
     * @return array
     */
    public static function getInstalled($app, $primary = false)
    {
        if (!$primary) {
            $primary = \App::$primary;
        }
        $system = !empty(Inji::$config['modules']) ? Inji::$config['modules'] : [];
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
    public static function getModuleControllers($moduleName)
    {
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
     * @param \App $app
     * @return \Module
     */
    public static function resolveModule($app)
    {
        if (!empty($app->params[0]) && $app->{$app->params[0]}) {
            $module = $app->{$app->params[0]};
            $module->params = array_slice($app->params, 1);
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
    public function getControllerPaths()
    {
        $paths = [];
        if (App::$cur != App::$primary) {
            if (!empty($this->params[0]) && strtolower($this->params[0]) != strtolower($this->moduleName)) {
                $paths['primaryAppAppTypePath_slice'] = App::$primary->path . '/modules/' . $this->moduleName . '/' . $this->app->type . 'Controllers/' . ucfirst($this->params[0]) . 'Controller.php';
                if (App::$primary->{$this->moduleName}) {
                    $paths['primaryAppAppTypePath_slice'] = App::$primary->{$this->moduleName}->path . '/' . $this->app->type . 'Controllers/' . ucfirst($this->params[0]) . 'Controller.php';
                }
            }
            $paths['primaryAppAppAppTypePath'] = App::$primary->path . '/modules/' . $this->moduleName . '/' . $this->app->type . 'Controllers/' . $this->moduleName . 'Controller.php';
            if (App::$primary->{$this->moduleName}) {
                $paths['primaryAppAppTypePath'] = App::$primary->{$this->moduleName}->path . '/' . $this->app->type . 'Controllers/' . $this->moduleName . 'Controller.php';
            }
            $paths['curAppAppTypePath'] = $this->app->{$this->moduleName}->path . '/' . $this->app->type . 'Controllers/' . $this->moduleName . 'Controller.php';
        }

        if (!empty($this->params[0]) && strtolower($this->params[0]) != strtolower($this->moduleName)) {
            $paths['appAppTypePath_slice'] = $this->app->path . '/modules/' . $this->moduleName . '/' . $this->app->type . 'Controllers/' . ucfirst($this->params[0]) . 'Controller.php';
            $paths['appTypePath_slice'] = $this->path . '/' . $this->app->type . 'Controllers/' . ucfirst($this->params[0]) . 'Controller.php';
        }
        $paths['appAppTypePath'] = $this->app->path . '/modules/' . $this->moduleName . '/' . $this->app->type . 'Controllers/' . $this->moduleName . 'Controller.php';
        $paths['appTypePath'] = $this->path . '/' . $this->app->type . 'Controllers/' . $this->moduleName . 'Controller.php';

        if (!empty($this->params[0]) && strtolower($this->params[0]) != strtolower($this->moduleName)) {
            $paths['appUniversalPath_slice'] = $this->app->path . '/modules/' . $this->moduleName . '/Controllers/' . ucfirst($this->params[0]) . 'Controller.php';
            $paths['universalPath_slice'] = $this->path . '/Controllers/' . ucfirst($this->params[0]) . 'Controller.php';
        }
        $paths['appUniversalPath'] = $this->app->path . '/modules/' . $this->moduleName . '/Controllers/' . $this->moduleName . 'Controller.php';
        $paths['universalPath'] = $this->path . '/Controllers/' . $this->moduleName . 'Controller.php';

        return $paths;
    }

    /**
     * Find controller by request
     * 
     * @return \Controller
     */
    public function findController()
    {
        $paths = $this->getControllerPaths();
        foreach ($paths as $pathName => $path) {
            if (file_exists($path)) {
                include $path;
                if (strpos($pathName, 'slice')) {
                    $controllerName = ucfirst($this->params[0]);
                    $params = array_slice($this->params, 1);
                } else {
                    $controllerName = $this->moduleName;
                    $params = $this->params;
                }
                $fullControllerName = $controllerName . 'Controller';
                $controller = new $fullControllerName();
                $controller->params = $params;
                $controller->module = $this;
                $controller->path = pathinfo($path, PATHINFO_DIRNAME);
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
    public static function getInfo($moduleName = '')
    {
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
    public function getSnippets($snippetsPath, $extensions = true, $dir = '/snippets', $moduleName = '')
    {
        $moduleName = $moduleName ? $moduleName : $this->moduleName;
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
     * Return extensions for type
     * 
     * @param string $extensionType
     * @param string $request
     * @return array
     */
    public function getExtensions($extensionType, $request)
    {
        $extensions = [];
        $modules = Module::getInstalled(App::$cur);
        $method = 'get' . ucfirst($extensionType);
        foreach ($modules as $module) {
            $extensions = array_merge($extensions, $this->{$method}($request, false, "/extensions/{$this->moduleName}/" . $extensionType, $module));
        }
        return $extensions;
    }

    function sitemap()
    {
        return [];
    }

}
