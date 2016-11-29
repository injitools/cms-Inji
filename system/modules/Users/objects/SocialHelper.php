<?php

/**
 * Social helper
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Users;

class SocialHelper extends \Object {

    public static function getObject() {
        $class = get_called_class();
        $class = substr($class, strrpos($class, '\\') + 1);
        $object = Social::get($class, 'object_name');
        return $object;
    }

    public static function getConfig() {
        $object = static::getObject();
        $configs = [];
        foreach ($object->configs as $config) {
            $configs[$config->name] = $config->value;
        }
        return $configs;
    }

    public static function checkAppAccess() {
        return false;
    }

}
