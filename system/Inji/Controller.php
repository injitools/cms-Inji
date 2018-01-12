<?php

namespace Inji;
/**
 * Controller
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class Controller {

    /**
     * Storage of cur requested controller
     *
     * @var Controller
     */
    public static $cur = null;

    /**
     * Requested params for method
     *
     * @var array
     */
    public $params = [];

    /**
     * Path to controller dir
     *
     * @var string
     */
    public $path = '';

    /**
     * Requested action name
     *
     * @var string
     */
    public $method = 'index';

    /**
     * Module of this controller
     *
     * @var Module
     */
    public $module = null;

    /**
     * This controller name
     *
     * @var string
     */
    public $name = '';

    /**
     * Flag of controller runing
     *
     * @var boolean
     */
    public $run = false;

    public $methodResolved = false;

    /**
     * Run controller
     */
    public function run() {
        !$this->methodResolved && $this->resolveMethod();
        if (!method_exists($this, $this->method . 'Action')) {
            INJI_SYSTEM_ERROR('method not found', true);
        }
        if (!$this->checkAccess()) {
            $msg = !empty($this->module->app->access->config['access']['accessTree'][App::$cur->type]['msg']) ? $this->module->app->access->config['access']['accessTree'][App::$cur->type]['msg'] : \I18n\Text::module('Access', 'noaccess');
            Tools::redirect($this->access->getDeniedRedirect(), $msg);
        }
        $this->run = true;
        $result = call_user_func_array([$this, $this->method . 'Action'], $this->params);
        if ($result && is_callable([$result, 'send'])) {
            $result->send();
        }
    }

    public function resolveMethod() {
        if ($this->methodResolved) {
            return true;
        }
        if (!empty($this->params[0]) && method_exists($this, $this->params[0] . 'Action')) {
            $this->method = $this->params[0];
            $this->params = array_slice($this->params, 1);
            $this->methodResolved = true;
            return true;
        }
        return false;
    }

    /**
     * Reference to short access core modules
     *
     * @param $name
     * @return Module|null
     */
    public function __get($name) {
        return App::$cur->__get($name);
    }

    /**
     * Reference to short access core modules
     *
     * @param $name
     * @param $params
     * @return null|Module
     */
    public function __call($name, $params) {
        return App::$cur->__call($name, $params);
    }

    /**
     * Check access to controller method
     *
     * @return boolean
     */
    public function checkAccess() {
        if ($this->module->app->access) {
            return $this->module->app->access->checkAccess($this);
        }
        return true;
    }

}
