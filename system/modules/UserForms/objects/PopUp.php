<?php

/**
 * PopUp
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace UserForms;

class PopUp {

    public static function onClick($userFormId, $name = null) {
        $userForm = Form::get($userFormId);
        if ($name === null) {
            $name = $userForm->name;
        }
        return "popUpForm({$userForm->id},'{$name}');";
    }

}
