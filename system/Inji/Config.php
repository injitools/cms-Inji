<?php

/**
 * Config
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class Config {

    /**
     * Static config storage
     * 
     * @var array 
     */
    private static $_configs = [];

    /**
     * Load system config
     * 
     * @return array
     */
    public static function system() {
        if (isset(self::$_configs['system'])) {
            return self::$_configs['system'];
        }

        if (!file_exists(INJI_SYSTEM_DIR . '/config/config.php')) {
            return [];
        }

        return self::$_configs['system'] = include INJI_SYSTEM_DIR . '/config/config.php';
    }

    /**
     * Load custom config
     * 
     * @param string $path
     * @return array
     */
    public static function custom($path) {
        if (isset(self::$_configs['custom'][$path])) {
            return self::$_configs['custom'][$path];
        }

        if (!file_exists($path)) {
                    return [];
        }

        return self::$_configs['custom'][$path] = include $path;
    }

    /**
     * Load app config
     * 
     * @param \App $app
     * @return array
     */
    public static function app($app = null) {
        if (!$app) {
            $app = App::$primary;
        }
        if (isset(self::$_configs['app'][$app->name])) {
            return self::$_configs['app'][$app->name];
        }

        $path = $app->path . "/config/config.php";
        if (!file_exists($path)) {
            return [];
        }

        return self::$_configs['app'][$app->name] = include $path;
    }

    /**
     * Load share config
     * 
     * @param string $module
     * @return array
     */
    public static function share($module = '') {
        if ($module) {
            if (isset(self::$_configs['shareModules'][$module])) {
                return self::$_configs['shareModules'][$module];
            }
            $path = INJI_PROGRAM_DIR . "/config/modules/{$module}.php";
        } else {
            if (isset(self::$_configs['share'])) {
                return self::$_configs['share'];
            }
            $path = INJI_PROGRAM_DIR . "/config/config.php";
        }
        if (!file_exists($path)) {
            if (file_exists(INJI_SYSTEM_DIR . "/modules/{$module}/defaultConfig.php")) {
                $path = INJI_SYSTEM_DIR . "/modules/{$module}/defaultConfig.php";
            } else {
                return [];
            }
        }

        if ($module) {
            return self::$_configs['shareModules'][$module] = include $path;
        } else {
            return self::$_configs['share'] = include $path;
        }
    }

    /**
     * Load module config
     * 
     * @param string $module_name
     * @param boolean $system
     * @param \App $app
     * @return array
     */
    public static function module($module_name, $system = false, $app = null) {

        if (!$app) {
            $app = App::$primary;
        }
        if ($system) {
            $appName = 'system';
            $appPath = INJI_SYSTEM_DIR;
        } else {
            $appName = $app->name;
            $appPath = $app->path;
        }

        if (isset(self::$_configs['module'][$appName][$module_name])) {
            return self::$_configs['module'][$appName][$module_name];
        }

        $path = $appPath . "/config/modules/{$module_name}.php";
        if (!file_exists($path)) {
            $path = INJI_SYSTEM_DIR . "/modules/{$module_name}/defaultConfig.php";
        }


        if (!file_exists($path)) {
            return [];
        }
        return self::$_configs['module'][$appName][$module_name] = include $path;
    }

    /**
     * Save config
     * 
     * @param string $type
     * @param array $data
     * @param string $module
     * @param \App $app
     */
    public static function save($type, $data, $module = '', $app = null) {
        if (!$app) {
            $app = App::$primary;
        }
        switch ($type) {
            case 'system':
                $path = INJI_SYSTEM_DIR . '/config/config.php';
                self::$_configs['system'] = $data;
                Inji::$inst->event('Config-change-system', $data);
                break;
            case 'app':
                $path = $app->path . "/config/config.php";
                self::$_configs['app'][$app->name] = $data;
                Inji::$inst->event('Config-change-app-' . $app->name, $data);
                break;
            case 'module':
                $path = $app->path . "/config/modules/{$module}.php";
                self::$_configs['module'][$app->name][$module] = $data;
                Inji::$inst->event('Config-change-module-' . $app->name . '-' . $module, $data);
                break;
            case 'share':
                if ($module) {
                    $path = INJI_PROGRAM_DIR . "/config/modules/{$module}.php";
                    self::$_configs['shareModules'][$module] = $data;
                    Inji::$inst->event('Config-change-shareModules-' . $module, $data);
                } else {
                    $path = INJI_PROGRAM_DIR . "/config/config.php";
                    self::$_configs['share'] = $data;
                    Inji::$inst->event('Config-change-share', $data);
                }
                break;
            default:
                $path = $type;
                self::$_configs['custom'][$path] = $data;
                break;
        }
        $text = "<?php\nreturn " . CodeGenerator::genArray($data);
        Tools::createDir(substr($path, 0, strripos($path, '/')));
        file_put_contents($path, $text);
    }

}
