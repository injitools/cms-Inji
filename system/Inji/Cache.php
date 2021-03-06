<?php

/**
 * Cache
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class Cache {

    /**
     * Connection to a set of memcache servers
     *
     * @var Memcached|Memcache
     */
    public static $server = null;

    /**
     * Truing to connect flag
     *
     * @var boolean
     */
    public static $connectTrying = false;

    /**
     * Connected flag
     *
     * @var boolean
     */
    public static $connected = false;

    /**
     * Try connect to memcache server
     */
    public static function connect() {
        if (!self::$connectTrying && class_exists('Memcached', false)) {
            self::$server = new Memcached();
            self::$server->addServer("127.0.0.1", 11211);
            self::$connected = @self::$server->addServer('localhost', 11211);
        }
        if (!self::$connectTrying && !self::$connected && class_exists('Memcache', false)) {
            self::$server = new Memcache();
            self::$connected = @self::$server->connect('localhost', 11211);
        }
        self::$connectTrying = true;
    }

    /**
     * Get chached value
     *
     * If value not present, call callback
     *
     * @param string $name
     * @param array $params
     * @param callable $callback
     * @return boolean
     */
    public static function get($name, $params = [], $callback = null, $lifeTime = 3600, $prefix = false) {
        if (!self::$connected) {
            self::connect();
        }
        if (!self::$connected) {
            if (is_callable($callback, true)) {
                return $callback($params);
            }
            return false;
        }
        if ($prefix === false) {
            $prefix = App::$primary->name;
        }

        $val = @self::$server->get($prefix . '_' . $name . serialize($params));
        if ($val !== false) {
            return $val;
        } else {
            if (is_callable($callback, true)) {
                while (!\Inji::$inst->blockParallel()) {
                    sleep(1);
                    $val = @self::$server->get($prefix . '_' . $name . serialize($params));
                    if ($val !== false) {
                        return $val;
                    }
                }
                $val = $callback($params);
                \Inji::$inst->unBlockParallel();
                self::set($name, $params, $val, $lifeTime, $prefix);
                return $val;
            }
        }
        return false;
    }

    /**
     * Set value to cache
     *
     * @param string $name
     * @param array $params
     * @param mixed $val
     * @param int $lifeTime
     * @return boolean
     */
    public static function set($name, $params = [], $val = '', $lifeTime = 3600, $prefix = false) {
        if (!self::$connected) {
            self::connect();
        }
        if (!self::$connected) {
            return false;
        }
        if ($prefix === false) {
            $prefix = App::$primary->name;
        }
        if (class_exists('Memcached', false) && self::$server instanceof Memcached) {
            return @self::$server->set($prefix . '_' . $name . serialize($params), $val, $lifeTime);
        } else {
            return @self::$server->set($prefix . '_' . $name . serialize($params), $val, false, $lifeTime);
        }
    }

    /**
     * Move file to cache folder and return path
     *
     * Also resize image when given resize params
     *
     * @param string $file
     * @param array $options
     * @return string
     */
    public static function file($file, $options = []) {
        $sizes = !empty($options['resize']) ? $options['resize'] : [];
        $crop = !empty($options['crop']) ? $options['crop'] : '';
        $pos = !empty($options['pos']) ? $options['pos'] : 'center';
        $fileinfo = pathinfo($file);
        $fileCheckSum = md5($fileinfo['dirname'] . filemtime($file));
        $path = static::getDir('static') . $fileCheckSum . '_' . $fileinfo['filename'];
        if ($sizes) {
            $path .= '.' . $sizes['x'] . 'x' . $sizes['y'] . $crop . $pos;
        }
        $path .= '.' . $fileinfo['extension'];
        if (!file_exists($path)) {
            copy($file, $path);
            if ($sizes) {
                Tools::resizeImage($path, $sizes['x'], $sizes['y'], $crop, $pos);
            }
        }

        return $path;
    }

    /**
     * Get cache dir for app
     *
     * @param App $app
     * @return string
     */
    public static function getDir($dirname, $app = null) {
        if (!$app) {
            $app = App::$primary;
        }
        $path = static::folder() . "{$dirname}/{$app->dir}/";
        Tools::createDir($path);
        return $path;
    }

    public static function folder() {
        return 'cache/';
    }
}