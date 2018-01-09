<?php

namespace Inji;

use Inji\Router\Folder;

/**
 * Router
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class Router {
    /**
     * @var Folder[]
     */
    public static $folders = [];
    public static $touchedModules = [];

    /**
     * @param string $folder
     * @param string $prefix
     * @param int|float $priority
     * @param int|bool $moduleIndex
     * @param array $moduleDirs
     */
    public static function addPath(string $folder, $prefix = '*', $priority = 0, $moduleIndex = false, $moduleDirs = []): void {
        self::$folders[] = new Folder($folder, $prefix, $priority, $moduleIndex, $moduleDirs);
        usort(self::$folders, function ($a, $b) {
            return $b->priority <=> $a->priority;
        });
    }

    /**
     * Find class by name
     *
     * @param string $className
     * @return bool
     */
    public static function findClass(string $className): bool {
        foreach (self::$folders as $folder) {
            $paths = $folder->forClass($className);
            foreach ($paths as $path) {
                if (file_exists($path->path)) {
                    self::loadClass($path->path);
                    if ($path->moduleName && !isset(static::$touchedModules[$path->moduleName])) {
                        static::$touchedModules[$path->moduleName] = true;
                        App::$cur->{$path->moduleName};
                    }
                }
            }
        }
        return false;
    }

    /**
     * Include class by name
     *
     * @param string $className
     * @return boolean
     */
    public static function loadClass($classPath) {
        include_once $classPath;
        return true;
    }


    /**
     * Return dir for class name
     *
     * @param string $className
     * @return string
     */
    public static function getLoadedClassPath($className) {
        $rc = new \ReflectionClass($className);
        return dirname($rc->getFileName());
    }

    public static function resolvePath($path) {
        $params = Tools::uriParse($path);
        if ($params[0] == App::$cur->name) {
            $app = App::$cur;
            $params = array_slice($params, 1);
        } else {
            $app = App::$primary;
        }
        $module = Module::resolveModule($app, $params);
        if (!$module) {
            return false;
        }
        $controller = $module->findController();
        if (!$controller) {
            return false;
        }
        $controller->resolveMethod();
        return compact('module', 'controller', 'params');
    }

}
