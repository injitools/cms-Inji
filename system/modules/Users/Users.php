<?php

/**
 * Users module
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class Users extends Module {

    public $cookiePrefix = '';

    public function init() {
        if (!empty($this->config['cookieSplit'])) {
            $this->cookiePrefix = \App::$cur->type;
        }
        \Users\User::$cur = new Users\User(array('group_id' => 1, 'role_id' => 1));
        if (!App::$cur->db->connect) {
            return false;
        }
        if (isset($_GET['logout'])) {
            return $this->logOut();
        }
        if (isset($_GET['passre']) && filter_input(INPUT_GET, 'user_mail')) {
            $this->passre(trim(filter_input(INPUT_GET, 'user_mail')));
        }
        if (!empty($_GET['passrecont']) && filter_input(INPUT_GET, 'hash')) {
            $this->passrecont(filter_input(INPUT_GET, 'hash'));
        }
        if (isset($_POST['autorization']) && trim(filter_input(INPUT_POST, 'user_login')) && trim(filter_input(INPUT_POST, 'user_pass'))) {
            unset($_POST['autorization']);
            return $this->autorization(trim(filter_input(INPUT_POST, 'user_login')), trim(filter_input(INPUT_POST, 'user_pass')), strpos(filter_input(INPUT_POST, 'user_login'), '@') ? 'mail' : 'login', false);
        }
        if (filter_input(INPUT_COOKIE, $this->cookiePrefix . '_user_session_hash') && filter_input(INPUT_COOKIE, $this->cookiePrefix . '_user_id')) {
            return $this->cuntinueSession(filter_input(INPUT_COOKIE, $this->cookiePrefix . '_user_session_hash'), filter_input(INPUT_COOKIE, $this->cookiePrefix . '_user_id'));
        }
    }

    public function logOut($redirect = true) {
        if (!empty($_COOKIE[$this->cookiePrefix . "_user_session_hash"]) && !empty($_COOKIE[$this->cookiePrefix . "_user_id"])) {
            $session = Users\Session::get([
                        ['user_id', $_COOKIE[$this->cookiePrefix . "_user_id"]],
                        ['hash', $_COOKIE[$this->cookiePrefix . "_user_session_hash"]]
            ]);
            if ($session) {
                $session->delete();
            }
        }
        if (!headers_sent()) {
            setcookie($this->cookiePrefix . "_user_session_hash", '', 0, "/");
            setcookie($this->cookiePrefix . "_user_id", '', 0, "/");
        }
        if ($redirect) {
            if (!empty($this->config['logoutUrl'][$this->app->type])) {
                Tools::redirect($this->config['logoutUrl'][$this->app->type]);
            }
            Tools::redirect('/', 'Вы вышли из своего профиля', 'success');
        }
    }

    public function cuntinueSession($hash, $userId) {
        $session = Users\Session::get([
                    ['user_id', $userId],
                    ['hash', $hash]
        ]);
        if ($session && $session->user && $session->user->blocked) {
            if (!headers_sent()) {
                setcookie($this->cookiePrefix . "_user_session_hash", '', 0, "/");
                setcookie($this->cookiePrefix . "_user_id", '', 0, "/");
            }
            Msg::add('Ваш аккаунт заблокирован', 'info');
            return;
        }
        if ($session && $session->user && !$session->user->blocked) {
            if (!empty($this->config['needActivation']) && $session->user->activation) {
                if (!headers_sent()) {
                    setcookie($this->cookiePrefix . "_user_session_hash", '', 0, "/");
                    setcookie($this->cookiePrefix . "_user_id", '', 0, "/");
                }
                Tools::redirect('/', 'Этот аккаунт ещё не активирован. <br />Если вы не получали письмо с ссылкой для активации, нажмите на - <a href = "/users/resendActivation/' . $session->user->id . '"><b>повторно выслать ссылку активации</b></a>');
            } elseif ($session->user->activation) {
                Msg::add('Этот аккаунт ещё не активирован, не все функции могут быть доступны. <br />Если вы не получали письмо с ссылкой для активации, нажмите на - <a href = "/users/resendActivation/' . $session->user->id . '"><b>повторно выслать ссылку активации</b></a>');
            }
            if (!$session->user->mail && !empty($this->config['noMailNotify'])) {
                Msg::add($this->config['noMailNotify']);
            }
            Users\User::$cur = $session->user;
            Users\User::$cur->date_last_active = 'CURRENT_TIMESTAMP';
            Users\User::$cur->save();
        } else {
            if (!headers_sent()) {
                setcookie($this->cookiePrefix . "_user_session_hash", '', 0, "/");
                setcookie($this->cookiePrefix . "_user_id", '', 0, "/");
            }
            Msg::add('Ваша сессия устарела или более недействительна, вам необходимо пройти <a href = "/users/login">авторизацию</a> заново', 'info');
        }
    }

    /**
     * @param string $user_mail
     */
    public function passre($user_mail) {
        $user = $this->get($user_mail, 'mail');
        if (!$user) {
            Msg::add('Пользователь ' . $user_mail . ' не найден, проверьте првильность ввода e-mail или зарегистрируйтесь', 'danger');
            return false;
        }
        $passre = Users\Passre::get([['user_id', $user->id], ['status', 1]]);
        if ($passre) {
            $passre->status = 2;
            $passre->save();
        }
        $hash = $user->id . '_' . Tools::randomString(50);
        $passre = new Users\Passre(['user_id' => $user->id, 'status' => 1, 'hash' => $hash]);
        $passre->save();
        Tools::sendMail('noreply@' . INJI_DOMAIN_NAME, $user_mail, 'Восстановление пароля на сайте ' . idn_to_utf8(INJI_DOMAIN_NAME), 'Было запрошено восстановление пароля на сайте ' . idn_to_utf8(INJI_DOMAIN_NAME) . '<br />для продолжения восстановления пароля перейдите по ссылке: <a href = "http://' . idn_to_utf8(INJI_DOMAIN_NAME) . '/?passrecont=1&hash=' . $hash . '">' . idn_to_utf8(INJI_DOMAIN_NAME) . '/?passrecont=1&hash=' . $hash . '</a>');
        Tools::redirect('/', 'На указанный почтовый ящик была выслана инструкция по восстановлению пароля', 'success');
    }

    public function passrecont($hash) {
        $passre = Users\Passre::get([['hash', $hash]]);
        if ($passre) {
            if ($passre->status != 1) {
                Tools::redirect('/', 'Этот код восстановление более недействителен', 'danger');
            }
            $passre->status = 3;
            $passre->save();
            $pass = Tools::randomString(10);
            $user = Users\User::get($passre->user_id);
            $user->pass = $this->hashpass($pass);
            $user->save();
            $this->autorization($user->id, $pass, 'id', true, true);
            Tools::sendMail('noreply@' . INJI_DOMAIN_NAME, $user->mail, 'Новый пароль на сайте ' . idn_to_utf8(INJI_DOMAIN_NAME), 'Было запрошено восстановление пароля на сайте ' . idn_to_utf8(INJI_DOMAIN_NAME) . '<br />Ваш новый пароль: ' . $pass);
            Tools::redirect('/', 'Вы успешно сбросили пароль и были авторизованы на сайте. На ваш почтовый ящик был выслан новый пароль', 'success');
        }
    }

    public function autorization($login, $pass, $ltype = 'login', $noMsg = true, $skipErrorCheck = false) {
        $user = $this->get($login, $ltype);
        if ($user && !$skipErrorCheck) {
            $lastSuccessLogin = \Users\User\LoginHistory::lastSuccessLogin($user->id);
            $where = [['user_id', $user->id]];
            if ($lastSuccessLogin) {
                $where[] = ['date_create', $lastSuccessLogin->date_create, '>'];
            }
            $loginHistoryErrorCount = \Users\User\LoginHistory::getCount(['where' => $where]);
            if ($loginHistoryErrorCount > 5) {
                Msg::add('Было совершено более 5ти попыток подбора пароля к вашему аккаунту, для вашей безопасности мы были вынуждены заблокировать к нему доступ.<br />
Для разблокировки аккаунта, воспользуйтесь <a href = "?passre=1&user_mail=' . $user->mail . '">Сбросом пароля</a>', 'danger');
                return false;
            }
        }
        if ($user && $this->verifypass($pass, $user->pass) && !$user->blocked) {
            $loginHistory = new \Users\User\LoginHistory([
                'user_id' => $user->id,
                'ip' => $_SERVER['REMOTE_ADDR'],
                'success' => true
            ]);
            $loginHistory->save();
            if (!empty($this->config['needActivation']) && $user->activation) {
                Tools::redirect('/', 'Этот аккаунт ещё не активирован. <br />Если вы не получали письмо с ссылкой для активации, нажмите на - <a href = "/users/resendActivation/' . $user->id . '"><b>повторно выслать ссылку активации</b></a>');
            } elseif ($user->activation) {
                Msg::add('Этот аккаунт ещё не активирован, не все функции могут быть доступны. <br />Если вы не получали письмо с ссылкой для активации, нажмите на - <a href = "/users/resendActivation/' . $user->id . '"><b>повторно выслать ссылку активации</b></a>');
            }
            if (!$user->mail && !empty($this->config['noMailNotify'])) {
                Msg::add($this->config['noMailNotify']);
            }
            $this->newSession($user);

            Users\User::$cur = $user;
            Users\User::$cur->date_last_active = 'CURRENT_TIMESTAMP';
            Users\User::$cur->save();
            if (!$noMsg && !empty($this->config['loginUrl'][$this->app->type])) {
                Tools::redirect($this->config['loginUrl'][$this->app->type]);
            }
            return true;
        }
        if (!$noMsg) {
            if ($user && $user->blocked) {
                Msg::add('Вы заблокированы', 'danger');
            } elseif ($user) {
                $loginHistory = new \Users\User\LoginHistory([
                    'user_id' => $user->id,
                    'ip' => $_SERVER['REMOTE_ADDR'],
                    'success' => false
                ]);
                $loginHistory->save();
                Msg::add('Вы ошиблись при наборе пароля или логина, попробуйте ещё раз или воспользуйтесь <a href = "?passre=1&user_mail=' . $user->mail . '">Восстановлением пароля</a>', 'danger');
            } else {
                Msg::add('Данный почтовый ящик не зарегистрирован в системе', 'danger');
            }
        }

        return false;
    }

    public function newSession($user) {
        do {
            $hash = Tools::randomString(255);
        } while (Users\Session::get($hash, 'hash'));

        $session = new Users\Session([
            'user_id' => $user->id,
            'agent' => $_SERVER['HTTP_USER_AGENT'],
            'ip' => $_SERVER['REMOTE_ADDR'],
            'hash' => $hash
        ]);
        $session->save();

        if (!headers_sent()) {
            setcookie($this->cookiePrefix . "_user_session_hash", $session->hash, time() + 360000, "/");
            setcookie($this->cookiePrefix . "_user_id", $session->user_id, time() + 360000, "/");
        } else {
            Msg::add('Не удалось провести авторизацию. Попробуйте позже', 'info');
        }
    }

    /**
     * Return user
     *
     * @param integer|string $idn
     * @param type $ltype
     * @return boolean|\User\User
     */
    public function get($idn, $ltype = 'id') {
        if (!$idn)
            return false;

        if (is_numeric($idn) && $ltype != 'login')
            $user = Users\User::get($idn, 'id');
        elseif ($ltype == 'login')
            $user = Users\User::get($idn, 'login');
        else
            $user = Users\User::get($idn, 'mail');
        if (!$user)
            return [];

        return $user;
    }

    public function registration($data, $autorization = false) {

        if (empty($data['user_mail'])) {
            Msg::add('Вы не ввели E-mail', 'danger');
            return false;
        }
        $data['user_mail'] = trim($data['user_mail']);
        if (!filter_var($data['user_mail'], FILTER_VALIDATE_EMAIL)) {
            Msg::add('Вы ввели не корректный E-mail', 'danger');
            return false;
        }

        $user = $this->get($data['user_mail'], 'mail');
        if ($user) {
            Msg::add('Введенный вами E-mail зарегистрирован в нашей системе, войдите или введите другой E-mail', 'danger');
            return false;
        }
        if (empty($data['user_login'])) {
            $data['user_login'] = $data['user_mail'];
        }
        $data['user_login'] = trim($data['user_login']);
        $user = $this->get($data['user_login'], 'login');
        if ($user) {
            Msg::add('Введенный вами логин зарегистрирован в нашей системе, войдите или введите другой логин', 'danger');
            return false;
        }
        if (empty($data['first_name'])) {
            $data['first_name'] = '';
        }
        if (empty($data['last_name'])) {
            $data['last_name'] = '';
        }
        if (!empty($data['user_name'])) {
            $data['first_name'] = $data['user_name'];
        }
        if (empty($data['user_city'])) {
            $data['user_city'] = '';
        }
        if (empty($data['user_birthday'])) {
            $data['user_birthday'] = '';
        }
        if (empty($data['user_phone'])) {
            $data['user_phone'] = '';
        }
        $invite_code = (!empty($data['invite_code']) ? $data['invite_code'] : (!empty($_POST['invite_code']) ? $_POST['invite_code'] : ((!empty($_COOKIE['invite_code']) ? $_COOKIE['invite_code'] : ((!empty($_GET['invite_code']) ? $_GET['invite_code'] : ''))))));
        if (!empty($invite_code)) {
            $invite = Users\User\Invite::get($invite_code, 'code');
            if (!$invite) {
                Msg::add('Такой код приглашения не найден', 'danger');
                return false;
            }
            if ($invite->limit && !($invite->limit - $invite->count)) {
                Msg::add('Лимит приглашений для данного кода исчерпан', 'danger');
                return false;
            }
            $data['parent_id'] = $invite->user_id;
            $inviter = $data['parent_id'];
            $invite->count++;
            $invite->save();
        }
        if (empty($data['parent_id']) && !empty($this->config['defaultPartner'])) {
            $data['parent_id'] = $this->config['defaultPartner'];
        }
        if (!empty($data['user_pass'])) {
            if (empty($data['user_pass'][0])) {
                Msg::add('Введите пароль', 'danger');
                return false;
            }
            if (empty($data['user_pass'][1])) {
                Msg::add('Повторите ввод пароля', 'danger');
                return false;
            }
            if ($data['user_pass'][0] != $data['user_pass'][1]) {
                Msg::add('Введенные пароли несовпадают', 'danger');
                return false;
            }
            $pass = $data['user_pass'][0];
        } else {
            $pass = Tools::randomString(10);
        }

        $user = new Users\User([
            'pass' => $this->hashpass($pass),
            'mail' => $data['user_mail'],
            'login' => htmlspecialchars($data['user_login']),
            'role_id' => 2,
            'group_id' => 2,
            'parent_id' => !empty($data['parent_id']) ? $data['parent_id'] : 0
        ]);
        if (!empty($this->config['needActivation'])) {
            $user->activation = Tools::randomString();
        }
        $user->save();
        if (!$user->id) {
            Msg::add('Не удалось зарегистрировать', 'danger');
            return false;
        }
        $info = new \Users\User\Info([
            'user_id' => $user->id,
            'first_name' => htmlspecialchars($data['first_name']),
            'last_name' => htmlspecialchars($data['last_name']),
            'city' => htmlspecialchars($data['user_city']),
            'bday' => htmlspecialchars($data['user_birthday']),
            'phone' => htmlspecialchars($data['user_phone']),
            'photo_file_id' => !empty($_FILES['user_photo']['tmp_name']) ? $this->files->upload($_FILES['user_photo']) : 0
        ]);
        $info->save();
        if (isset($inviter)) {
            $this->AddUserActivity($inviter, 2, "У вас зарегистрировался новый партнер, {$info->first_name} {$info->last_name} (id: {$user->id}, email: {$user->mail})");
        }
        if ($autorization) {
            $this->autorization($data['user_mail'], $pass, 'mail');
        }
        if (!empty($this->config['needActivation'])) {
            $from = 'noreply@' . INJI_DOMAIN_NAME;
            $to = $data['user_mail'];
            $subject = 'Регистрация на сайте ' . idn_to_utf8(INJI_DOMAIN_NAME);
            $text = 'Вы были зарегистрированы на сайте ' . idn_to_utf8(INJI_DOMAIN_NAME) . '<br />для входа используйте ваш почтовый ящик в качестве логина и пароль: ' . $pass;
            $text .= '<br />';
            $text .= '<br />';
            $text .= 'Для активации вашего аккаунта перейдите по ссылке <a href = "http://' . INJI_DOMAIN_NAME . '/users/activation/' . $user->id . '/' . $user->activation . '">http://' . idn_to_utf8(INJI_DOMAIN_NAME) . '/users/activation/' . $user->id . '/' . $user->activation . '</a>';
            Tools::sendMail($from, $to, $subject, $text);
            Msg::add('Вы были зарегистрированы. На указанный почтовый ящик был выслан ваш пароль и ссылка для активации', 'success');
        } else {
            $from = 'noreply@' . INJI_DOMAIN_NAME;
            $to = $data['user_mail'];
            $subject = 'Регистрация на сайте ' . idn_to_utf8(INJI_DOMAIN_NAME);
            $text = 'Вы были зарегистрированы на сайте ' . idn_to_utf8(INJI_DOMAIN_NAME) . '<br />для входа используйте ваш почтовый ящик в качестве логина и пароль: ' . $pass;
            Tools::sendMail($from, $to, $subject, $text);
            Msg::add('Вы были зарегистрированы. На указанный почтовый ящик был выслан ваш пароль', 'success');
        }
        return $user->id;
    }

    public function hashpass($pass) {
        return password_hash($pass, PASSWORD_DEFAULT);
    }

    public function verifypass($pass, $hash) {
        return password_verify($pass, $hash);
    }

    public function getUserPartners($user, $levelsCount = 0) {
        $return = [
            'users' => [],
            'levels' => [],
            'count' => 0,
            'lastLevel' => 0
        ];
        $userIds = $user->user_id;
        for ($i = 1; $i <= $levelsCount || !$levelsCount; $i++) {
            if (!$userIds && $levelsCount) {
                $return['levels'][$i] = [];
                continue;
            } elseif (!$userIds && !$levelsCount) {
                break;
            }
            $usersLevel = \Users\User::getList(['where' => [['parent_id', $userIds, 'IN']]]);
            $return['users'] += $usersLevel;
            $return['levels'][$i] = array_keys($usersLevel);
            $userIds = implode(',', $return['levels'][$i]);
            $return['lastLevel'] = $i;
        }
        $return['count'] = count($return['users']);
        return $return;
    }

    /**
     * @param integer $cat_id
     */
    public function addUserActivity($user_id, $cat_id, $text = '') {
        $ua = new Users\Activity([
            'user_id' => $user_id,
            'category_id' => $cat_id,
            'text' => $text,
        ]);
        $ua->save();
    }
}