<?php

/**
 * INJI
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2017 Alexey Krupskiy
 * @license https://github.com/injitools/Inji/blob/master/LICENSE
 */
class ServerController extends Controller {
    function csrfAction($key = '') {
        $result = new \Server\Result();
        $key = (string)$key;
        if (!$key) {
            $result->success = false;
            return $result->send();
        }
        $_SESSION['csrf'][$key] = Tools::randomString();
        $result->content = $_SESSION['csrf'][$key];
        $result->send();
    }
}