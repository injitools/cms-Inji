<?php

/**
 * Result
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Server;

class Result {

    public $content = null;
    public $success = true;
    public $successMsg = '';
    public $commands = [];
    public $scripts = [];
    public $asObject = false;

    public function send($applyCallback = false) {
        $return = [];
        $return['success'] = $this->success;
        if ($this->success) {
            $return['content'] = $this->content;
            $return['successMsg'] = $this->successMsg;
        } else {
            $return['error'] = $this->content;
        }
        if (!headers_sent()) {
            header('Content-type: application/json');
        }
        $return['commands'] = $this->commands;
        $return['scripts'] = \App::$cur->view->getScripts();
        if ($applyCallback && !empty($_GET['callback'])) {
            echo $_GET['callback'] . '(';
        }
        echo json_encode($return, $this->asObject ? JSON_FORCE_OBJECT : null);
        if ($applyCallback && !empty($_GET['callback'])) {
            echo ')';
        }

        \Inji::$inst->stop();
        return true;
    }
}