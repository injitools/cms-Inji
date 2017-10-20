<?php

/**
 * Active form input  pasword changer
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ui\ActiveForm\Input;

class ChangePassword extends \Ui\ActiveForm\Input {

    public function parseRequest($request) {
        if (!empty($request[$this->colName]['pass'])) {
            $this->activeForm->model->{$this->colName} = \App::$cur->users->hashpass($request[$this->colName]['pass']);
        }
    }

    public function validate(&$request) {
        if (
            (!empty($this->colParams['required']) && (empty($request[$this->colName]['pass']) || empty($request[$this->colName]['repeat']))) ||
            (!empty($this->colParams['requiredOnNew']) && !$this->activeForm->model->pk() && (empty($request[$this->colName]['pass']) || empty($request[$this->colName]['repeat']))) ||
            (!empty($request[$this->colName]['pass']) && empty($request[$this->colName]['repeat'])) ||
            (empty($request[$this->colName]['pass']) && !empty($request[$this->colName]['repeat']))
        ) {
            if (empty($request[$this->colName]['pass'])) {
                throw new \Exception('Вы не ввели новый пароль');
            }
            if (empty($request[$this->colName]['repeat'])) {
                throw new \Exception('Вы не ввели подтверждение нового пароля');
            }
            if ($request[$this->colName]['pass'] != $request[$this->colName]['repeat']) {
                throw new \Exception('Введенные пароли не совпадают');
            }
        }

        if (!empty($this->colParams['validator'])) {
            $modelName = $this->modelName;
            $validator = $modelName::validator($this->colParams['validator']);
            $validator($this->activeForm, $request);
        }
    }
}
