<?php

/**
 * Server module
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class Server extends Module {
    function checkCsrf($key, $token) {
        return !empty($_SESSION['csrf'][$key]) && $_SESSION['csrf'][$key] == $token;
    }

    function checkCsrfForm($formData) {
        if (empty($formData['csrfKey']) || empty($formData['csrfToken'])) {
            return false;
        }
        return $this->checkCsrf($formData['csrfKey'], $formData['csrfToken']);
    }
}
