<?php
namespace Inji\I18n;
use Inji\App;
use Inji\InjiObject;

class Text extends InjiObject {
    public static $strings = [];

    public static function module($module, $code, $params = [], $default = false, $lang = false) {
        $paramsKeys = array_keys($params);
        foreach ($paramsKeys as &$paramsKey) {
            $paramsKey = '${' . $paramsKey . '}';
        }
        $text = static::findString($module, $code, $lang);
        if ($text === false) {
            $text = $default !== false ? $default : $code;
        }
        return str_replace($paramsKeys, $params, $text);
    }

    public static function findString($module, $code, $lang = false) {
        $lang = $lang ? $lang : App::$cur->i18n->lang();
        return static::loadStrings($module, $lang) && isset(static::$strings[$module][$lang][$code]) ? static::$strings[$module][$lang][$code] : false;
    }

    public static function loadStrings($module, $lang = false) {
        $lang = $lang ? $lang : App::$cur->i18n->lang();
        $modulePaths = array_reverse(\Module::getModulePaths($module), true);
        if (isset(static::$strings[$module][$lang])) {
            return true;
        }
        if (!isset(static::$strings[$module])) {
            static::$strings[$module] = [];
        }
        foreach ($modulePaths as $modulePath) {
            if (file_exists($modulePath . '/i18n/' . $lang . '.php')) {
                if (!isset(static::$strings[$module][$lang])) {
                    static::$strings[$module][$lang] = include $modulePath . '/i18n/' . $lang . '.php';
                } else {
                    static::$strings[$module][$lang] = array_merge(static::$strings[$module][$lang], include $modulePath . '/i18n/' . $lang . '.php');
                }
            }
        }
        return isset(static::$strings[$module][$lang]);
    }
}