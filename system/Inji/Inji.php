<?php

/**
 * Inji core
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class Inji {

    /**
     * Static storage for core object
     * 
     * @var Inji
     */
    public static $inst = null;

    /**
     * Dynamic events listeners
     * 
     * @var array
     */
    private $_listeners = [];

    /**
     * Core config
     * 
     * @var array
     */
    public static $config = [];

    /**
     * Static storage for anything
     * 
     * @var array 
     */
    public static $storage = [];

    /**
     * Stop executing code if this true after use Inji::$inst->stop() constuction in code
     * 
     * @var boolean
     */
    public $exitOnStop = true;

    /**
     * Add event listener
     * 
     * @param string $eventName
     * @param string $listenCode
     * @param array|closure $callback
     * @param boolean $save
     */
    public function listen($eventName, $listenCode, $callback, $save = false) {
        if ($save) {
            $config = Config::custom(App::$primary->path . '/config/events.php');
            $config[$eventName][$listenCode] = serialize($callback);
            Config::save(App::$primary->path . '/config/events.php', $config);
        } else {
            $this->_listeners[$eventName][$listenCode] = $callback;
        }
    }

    /**
     * Throw event
     * 
     * @param string $eventName
     * @param mixed $eventObject
     * @return mixed
     */
    public function event($eventName, $eventObject = null) {
        $event = [
            'eventName' => $eventName,
            'eventObject' => $eventObject,
        ];

        $listeners = [];
        if (!empty($this->_listeners[$eventName])) {
            $listeners = $this->_listeners[$eventName];
        }
        $config = Config::custom(App::$primary->path . '/config/events.php');
        if (!empty($config[$eventName])) {
            foreach ($config[$eventName] as $listenCode => $callback) {
                $listeners[$listenCode] = (@unserialize($callback) !== false) ? unserialize($callback) : $callback;
            }
        }
        if ($listeners) {
            $iteration = 0;
            $calledBefore = [];
            foreach ($listeners as $listenCode => $callback) {
                $event['iteration'] = ++$iteration;
                $event['calledBefore'] = $calledBefore;
                if (is_callable($callback)) {
                    $event['eventObject'] = $callback($event);
                } elseif (is_array($callback) && isset($callback['callback'])) {
                    $event['eventObject'] = $callback['callback']($event, $callback);
                } else {
                    $event['eventObject'] = App::$cur->{$callback['module']}->{$callback['method']}($event, $callback);
                }
                $calledBefore[$iteration] = $listenCode;
            }
        }
        return $event['eventObject'];
    }

    /**
     * Unlisten event
     * 
     * @param string $eventName
     * @param string $listenCode
     * @param boolean $save
     */
    public function unlisten($eventName, $listenCode, $save = false) {
        if ($save) {
            $config = Config::custom(App::$primary->path . '/config/events.php');
            if (!empty($config[$eventName][$listenCode])) {
                unset($config[$eventName][$listenCode]);
                Config::save(App::$primary->path . '/config/events.php', $config);
            }
        }
        if (!empty($this->_listeners[$eventName][$listenCode])) {
            unset($this->_listeners[$eventName][$listenCode]);
        }
    }

    public function stop() {
        if ($this->exitOnStop) {
            exit();
        }
        return false;
    }
}