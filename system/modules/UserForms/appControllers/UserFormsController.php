<?php

/**
 * User forms app controller
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class UserFormsController extends Controller {

    public function getFormHtmlAction($userForm_id) {
        $this->view->widget('UserForms\userForm', ['formId' => $userForm_id]);
    }
}