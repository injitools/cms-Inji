<?php

/**
 * Users app controller
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class UsersController extends Controller
{
    function indexAction()
    {
        Tools::redirect('/users/cabinet/profile');
    }

    function cabinetAction($activeSection = '')
    {
        $bread = [];

        $sections = $this->module->getSnippets('cabinetSection');
        if (!empty($sections[$activeSection]['name'])) {
            $this->view->setTitle($sections[$activeSection]['name'] . ' - Личный кабинет');
            $bread[] = ['text' => 'Личный кабинет', 'href' => '/users/cabinet'];
            $bread[] = ['text' => $sections[$activeSection]['name']];
        } else {
            $this->view->setTitle('Личный кабинет');
            $bread[] = ['text' => 'Личный кабинет'];
        }
        $this->view->page(['data' => compact('widgets', 'sections', 'activeSection', 'bread')]);
    }

    function loginAction()
    {
        $this->view->setTitle('Авторизация');
        $bread = [];
        $bread[] = ['text' => 'Авторизация'];
        $this->view->page(['data' => compact('bread')]);
    }

    function registrationAction()
    {
        $this->view->setTitle('Регистрация');
        if (Users\User::$cur->user_id) {
            Tools::redirect('/', 'Вы уже зарегистрированы');
        }
        if (!empty($_POST)) {
            $error = false;
            if ($this->Recaptcha) {
                $response = $this->Recaptcha->check($_POST['g-recaptcha-response']);
                if ($response) {
                    if (!$response->success) {
                        Msg::add('Вы не прошли проверку на робота', 'danger');
                        $error = true;
                    }
                } else {
                    Msg::add('Произошла ошибка, попробуйте ещё раз');
                    $error = true;
                }
            }
            if (!$error) {
                if ($this->Users->registration($_POST)) {
                    Tools::redirect('/');
                }
            }
        }
        $this->view->setTitle('Регистрация');
        $bread = [];
        $bread[] = ['text' => 'Регистрация'];
        $this->view->page(['data' => compact('bread')]);
    }

    function activationAction($userId = 0, $hash = '')
    {
        $user = \Users\User::get((int) $userId);
        if (!$user || !$hash || $user->activation !== (string) $hash) {
            Tools::redirect('/', 'Во время активации произошли ошибки', 'danger');
        }
        $user->activation = '';
        $user->save();
        Inji::$inst->event('Users-completeActivation', $user);
        Tools::redirect('/', 'Вы успешно активировали ваш аккаунт', 'success');
    }

    function attachEmailAction()
    {
        if (Users\User::$cur->mail) {
            Tools::redirect('/', 'К вашему аккаунту уже привязан E-Mail');
        }
        if (!empty($_POST['mail'])) {
            $user_mail = trim($_POST['mail']);
            if (!filter_var($user_mail, FILTER_VALIDATE_EMAIL)) {
                Msg::add('Вы ввели не корректный E-mail', 'danger');
            } else {
                $user = Users\User::get($user_mail, 'mail');
                if ($user && $user->id != Users\User::$cur->id) {
                    Msg::add('Данный E-mail уже привязан к другому аккаунту', 'danger');
                } else {
                    Users\User::$cur->mail = $user_mail;
                    if (!empty($this->module->config['needActivation'])) {
                        Users\User::$cur->activation = Tools::randomString();
                        $from = 'noreply@' . INJI_DOMAIN_NAME;
                        $to = $user_mail;
                        $subject = 'Активация аккаунта на сайте ' . idn_to_utf8(INJI_DOMAIN_NAME);
                        $text = 'Для активации вашего аккаунта перейдите по ссылке <a href = "http://' . INJI_DOMAIN_NAME . '/users/activation/' . Users\User::$cur->id . '/' . Users\User::$cur->activation . '">http://' . idn_to_utf8(INJI_DOMAIN_NAME) . '/users/activation/' . Users\User::$cur->id . '/' . Users\User::$cur->activation . '</a>';
                        Tools::sendMail($from, $to, $subject, $text);
                        Msg::add('На указанный почтовый ящик была выслана ваша ссылка для подтверждения E-Mail', 'success');
                    } else {
                        Msg::add('Вы успешно привязали E-Mail к своему аккаунту', 'success');
                    }
                    Users\User::$cur->save();
                    Tools::redirect('/');
                }
            }
        }
        $this->view->page();
    }

    function resendActivationAction($userId = 0)
    {
        $user = \Users\User::get((int) $userId);
        if (!$user) {
            Tools::redirect('/', 'Не указан пользователь', 'danger');
        }
        if (!$user->activation) {
            Tools::redirect('/', 'Пользователь уже активирован');
        }
        $from = 'noreply@' . INJI_DOMAIN_NAME;
        $to = $user->mail;
        $subject = 'Активация аккаунта на сайте ' . idn_to_utf8(INJI_DOMAIN_NAME);
        $text = 'Для активации вашего аккаунта перейдите по ссылке <a href = "http://' . INJI_DOMAIN_NAME . '/users/activation/' . $user->id . '/' . $user->activation . '">http://' . idn_to_utf8(INJI_DOMAIN_NAME) . '/users/activation/' . $user->id . '/' . $user->activation . '</a>';
        Tools::sendMail($from, $to, $subject, $text);
        Tools::redirect('/', 'На указанный почтовый ящик была выслана ваша ссылка для подтверждения E-Mail', 'success');
    }

    function getPartnerInfoAction($userId = 0)
    {
        $userId = (int) $userId;
        $result = new \Server\Result();
        if (!$userId) {
            $result->success = FALSE;
            $result->content = 'Не указан пользователь';
            $result->send();
        }
        $partners = App::$cur->users->getUserPartners(Users\User::$cur, 8);
        if (empty($partners['users'][$userId])) {
            $result->success = FALSE;
            $result->content = 'Этот пользователь не находится в вашей структуре';
            $result->send();
        }
        $user = $partners['users'][$userId];
        ob_start();
        echo "id:{$user->id}<br />";
        echo "E-mail: <a href='mailto:{$user->mail}'>{$user->mail}</a>";
        $rewards = Money\Reward::getList(['where' => ['active', 1]]);
        $levelTypes = [
            'procent' => 'Процент',
            'amount' => 'Сумма',
        ];
        $itemTypes = [
            'event' => 'Событие'
        ];
        foreach ($rewards as $reward) {
            foreach ($reward->conditions as $condition) {
                $complete = $condition->checkComplete($userId);
                ?>
                <h5 class="<?= $complete ? 'text-success' : 'text-danger'; ?>"><?= $condition->name(); ?></h5>
                <ul>
                  <?php
                  foreach ($condition->items as $item) {
                      $itemComplete = $item->checkComplete($userId);
                      switch ($item->type) {
                          case 'event':
                              $name = \Events\Event::get($item->value, 'event')->name();
                              break;
                      }
                      ?>
                      <li> 
                        <b class="<?= $itemComplete ? 'text-success' : 'text-danger'; ?>"><?= $name; ?> <?= $item->recivedCount($userId); ?></b>/<?= $item->count; ?> <br />
                      </li>
                      <?php
                  }
                  ?>
                </ul>
                <?php
            }
        }
        $result->content = ob_get_contents();
        ob_end_clean();
        $result->send();
    }

}
