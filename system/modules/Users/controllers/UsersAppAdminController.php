<?php

/**
 * Users admin controller
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\Users;
use Inji\Admin\AdminController;

class UsersAppAdminController extends AdminController {

    public function loginAction() {
        if (!Users\User::$cur->user_id) {
            $this->view->page(['page' => 'login', 'content' => 'login']);
        } else {
            $this->view->page(['content' => 'profile']);
        }
    }

    public function loginAsAction($userId) {
        $user = Users\User::get($userId);
        App::$cur->users->newSession($user);
        Tools::redirect('/', 'Теперь вы на сайте под пользователем ' . $user->name());
    }

}
