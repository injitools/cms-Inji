<?php

/**
 * Exchange1c app controller
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class Exchange1cController extends Controller {

    public function indexAction() {
        ini_set('memory_limit', '4000M');
        ignore_user_abort(true);
        set_time_limit(0);
        Model::$logging = false;
        if (empty($_SESSION['auth'])) {
            if (empty($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
                header('WWW-Authenticate: Basic realm="exchange1c"');
                header('HTTP/1.0 401 Unauthorized');
                $this->module->response('failure', 'Not isset PHP_AUTH_USER or PHP_AUTH_PW');
            }
            if ($_SERVER['PHP_AUTH_USER'] !== $this->module->config['user']['login'] || $_SERVER['PHP_AUTH_PW'] !== $this->module->config['user']['password']) {
                $this->module->response('failure', 'User or password not correct');
            }
        }
        if (empty($_GET['mode'])) {
            $this->module->response('failure', 'Mode missing');
        }
        $exchange = \Exchange1c\Exchange::get(['session', session_id()]);
        if (!$exchange) {
            $exchange = new \Exchange1c\Exchange();
            $exchange->type = $_GET['type'];
            $exchange->session = session_id();
            $exchange->save();
            $exchange->path = App::$cur->path . '/tmp/Exchange1c/' . date('Y-m-d') . '/' . $exchange->id;
            $exchange->save();
        }

        $log = new \Exchange1c\Exchange\Log();
        $log->exchange_id = $exchange->id;
        $log->type = $_GET['type'];
        $log->info = $_GET['mode'];
        $log->status = 'process';
        $log->query = json_encode($_GET);
        $log->save();
        $modeClass = 'Exchange1c\Mode\\' . ucfirst(strtolower($_GET['mode']));
        if (!class_exists($modeClass)) {
            $log->status = 'failure';
            $log->info = 'mode class ' . $modeClass . ' not found';
            $log->date_end = date('Y-m-d H:i:s');
            $log->save();
        }
        $mode = new $modeClass;
        $mode->exchange = $exchange;
        $mode->log = $log;
        $mode->process();
        Model::$logging = true;
    }

}
