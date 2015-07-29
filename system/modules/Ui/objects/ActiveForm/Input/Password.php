<?php

/**
 * Html input
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ui\ActiveForm\Input;

class Password extends \Ui\ActiveForm\Input {

    function parseRequest($request) {
        if (!empty($request[$this->colName]['pass']) && !empty($request[$this->colName]['pass'])) {
            if (empty($request[$this->colName]['pass'])) {
                \Msg::add('Вы не ввели пароль в первое поле', 'danger');
                return FALSE;
            }
            if (empty($request[$this->colName]['repeat'])) {
                \Msg::add('Вы не ввели пароль во второе поле', 'danger');
                return FALSE;
            }
            if ($request[$this->colName]['pass'] != $request[$this->colName]['repeat']) {
                \Msg::add('Введенные пароли не совадают', 'danger');
                return FALSE;
            }
            $this->activeForm->model->{$this->colName} = \App::$cur->users->hashpass($request[$this->colName]['pass']);
        }
    }

}
