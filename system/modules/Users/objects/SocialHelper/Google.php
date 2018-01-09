<?php

/**
 * Social helper Google
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Users\SocialHelper;

class Google extends \Users\SocialHelper {

    public static function auth() {

        $config = static::getConfig();
        if (empty($_GET['code']) && empty($_GET['error'])) {
            $query = [
                'client_id' => $config['client_id'],
                'scope' => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile',
                'response_type' => 'code',
                'redirect_uri' => 'http://' . INJI_DOMAIN_NAME . '/users/social/auth/google'
            ];
            \Inji\Tools::redirect("https://accounts.google.com/o/oauth2/auth?" . http_build_query($query));
        }
        if (empty($_GET['code']) && !empty($_GET['error'])) {
            \Inji\Tools::redirect('/', 'Произошла ошибка во время авторизации через соц. сеть: ' . $_GET['error_description']);
        }
        $query = [
            'client_id' => $config['client_id'],
            'client_secret' => $config['secret'],
            'code' => $_GET['code'],
            'grant_type' => 'authorization_code',
            'redirect_uri' => 'http://' . INJI_DOMAIN_NAME . '/users/social/auth/google'
        ];
        $result = false;
        if ($curl = curl_init()) {
            curl_setopt($curl, CURLOPT_URL, 'https://accounts.google.com/o/oauth2/token');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($query));
            $result = curl_exec($curl);
            curl_close($curl);
        }
        if ($result === false) {
            \Inji\Tools::redirect('/', 'Во время авторизации произошли ошибки', 'danger');
        }
        $result = json_decode($result, true);
        if (empty($result['access_token'])) {
            \Inji\Tools::redirect('/', 'Во время авторизации произошли ошибки', 'danger');
        }
        $userQuery = [
            'access_token' => $result['access_token']
        ];
        $userResult = @file_get_contents("https://www.googleapis.com/oauth2/v1/userinfo?" . http_build_query($userQuery));
        if (!$userResult) {
            \Inji\Tools::redirect('/', 'Во время авторизации произошли ошибки', 'danger');
        }
        $userDetail = json_decode($userResult, true);
        if (empty($userDetail['id'])) {
            \Inji\Tools::redirect('/', 'Во время авторизации произошли ошибки', 'danger');
        }
        $social = static::getObject();
        $userSocial = \Users\User\Social::get([['uid', $userDetail['id']], ['social_id', $social->id]]);
        if ($userSocial && $userSocial->user) {
            \App::$cur->users->newSession($userSocial->user);
            if (!empty(\App::$cur->users->config['loginUrl'][\App::$cur->type])) {
                \Inji\Tools::redirect(\App::$cur->users->config['loginUrl'][\App::$cur->type]);
            }
        } else {
            if ($userSocial && !$userSocial->user) {
                $userSocial->delete();
            }
            if (!\Users\User::$cur->id) {
                $user = false;
                if (!empty($userDetail['email']) && !empty($userDetail['verified_email'])) {
                    $user = \Users\User::get($userDetail['email'], 'mail');
                }
                if (!$user) {
                    $user = new \Users\User();
                    $user->group_id = 2;
                    $user->role_id = 2;
                    if (!empty($userDetail['email']) && !empty($userDetail['verified_email'])) {
                        $user->login = $user->mail = $userDetail['email'];
                    }
                    $invite_code = (!empty($_POST['invite_code']) ? $_POST['invite_code'] : ((!empty($_COOKIE['invite_code']) ? $_COOKIE['invite_code'] : ((!empty($_GET['invite_code']) ? $_GET['invite_code'] : '')))));
                    if (!empty($invite_code)) {
                        $invite = \Users\User\Invite::get($invite_code, 'code');
                        $inveiteError = false;
                        if (!$invite) {
                            \Inji\Msg::add('Такой код пришлашения не найден', 'danger');
                            $inveiteError = true;
                        }
                        if ($invite->limit && !($invite->limit - $invite->count)) {
                            \Inji\Msg::add('Лимит приглашений для данного кода исчерпан', 'danger');
                            $inveiteError = true;
                        }
                        if (!$inveiteError) {
                            $user->parent_id = $invite->user_id;
                            $invite->count++;
                            $invite->save();
                        }
                    }
                    if (!$user->parent_id && !empty(\App::$cur->Users->config['defaultPartner'])) {
                        $user->parent_id = \App::$cur->Users->config['defaultPartner'];
                    }
                    $user->save();
                    $userInfo = new \Users\User\Info();
                    $userInfo->user_id = $user->id;
                    $userInfo->save();
                }
            } else {
                $user = \Users\User::$cur;
            }
            if (!$user->info->photo_file_id && !empty($userDetail['picture'])) {
                $user->info->photo_file_id = \App::$cur->files->uploadFromUrl($userDetail['picture']);
            }
            if (!$user->info->first_name && !empty($userDetail['given_name'])) {
                $user->info->first_name = $userDetail['given_name'];
            }
            if (!$user->info->last_name && !empty($userDetail['family_name'])) {
                $user->info->last_name = $userDetail['family_name'];
            }
            if (!$user->info->sex && !empty($userDetail['gender'])) {
                $user->info->sex = $userDetail['gender'] == 'male' ? 1 : ($userDetail['gender'] == 'female' ? 2 : 0);
            }
            $user->info->save();
            $userSocial = new \Users\User\Social();
            $userSocial->uid = $userDetail['id'];
            $userSocial->social_id = $social->id;
            $userSocial->user_id = $user->id;
            $userSocial->save();
            \App::$cur->users->newSession($user);
            \Inji\Tools::redirect('/users/cabinet/profile', 'Вы успешно зарегистрировались через Google+', 'success');
        }
    }

}
