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

class Result extends \Object {

    public $content = null;
    public $success = true;
    public $successMsg = '';
    public $commands = [];
    public $scripts = [];

    public function send() {
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
        echo json_encode($return);
        exit();
    }

}
