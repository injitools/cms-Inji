<?php

/**
 * Social helper vk
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Users\SocialHelper;

class Facebook extends \Users\SocialHelper {

    public static function auth() {
        $config = static::getConfig();
        if (empty($_GET['code']) && empty($_GET['error'])) {
            $query = [
                'client_id' => $config['appId'],
                'scope' => 'email',
                'response_type' => 'code',
                'redirect_uri' => 'http://' . INJI_DOMAIN_NAME . '/users/social/auth/facebook'
            ];
            \Inji\Tools::redirect("https://www.facebook.com/dialog/oauth?" . http_build_query($query));
        }
        if (empty($_GET['code']) && !empty($_GET['error'])) {
            \Inji\Tools::redirect('/', 'Произошла ошибка во время авторизации через соц. сеть: ' . $_GET['error_description']);
        }
        $query = [
            'client_id' => $config['appId'],
            'redirect_uri' => 'http://' . INJI_DOMAIN_NAME . '/users/social/auth/facebook',
            'client_secret' => $config['secret'],
            'code' => urldecode($_GET['code']),
        ];
        $result = @file_get_contents("https://graph.facebook.com/oauth/access_token?" . http_build_query($query));
        if ($result === false) {
            \Inji\Tools::redirect('/', 'Во время авторизации произошли ошибки', 'danger');
        }
        parse_str($result, $output);
        if (empty($output['access_token'])) {
            \Inji\Tools::redirect('/', 'Во время авторизации произошли ошибки', 'danger');
        }
        $userQuery = [
            'access_token' => $output['access_token'],
            'fields' => 'first_name,middle_name,last_name,email,gender,location,picture'
        ];
        $userResult = @file_get_contents("https://graph.facebook.com/me?" . http_build_query($userQuery));
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
                if (!empty($userDetail['email'])) {
                    $user = \Users\User::get($userDetail['email'], 'mail');
                }
                if (!$user) {
                    $user = new \Users\User();
                    $user->group_id = 2;
                    $user->role_id = 2;
                    if (!empty($userDetail['email'])) {
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
            if (!$user->info->photo_file_id && !empty($userDetail['picture']['data']['url'])) {
                $user->info->photo_file_id = \App::$cur->files->uploadFromUrl($userDetail['picture']['data']['url']);
            }
            if (!$user->info->first_name && !empty($userDetail['first_name'])) {
                $user->info->first_name = $userDetail['first_name'];
            }
            if (!$user->info->last_name && !empty($userDetail['last_name'])) {
                $user->info->last_name = $userDetail['last_name'];
            }
            if (!$user->info->middle_name && !empty($userDetail['middle_name'])) {
                $user->info->middle_name = $userDetail['middle_name'];
            }
            if (!$user->info->city && !empty($userDetail['location'])) {
                $user->info->city = $userDetail['location'];
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
            if (!empty(\App::$cur->users->config['loginUrl'][\App::$cur->type])) {
                \Inji\Tools::redirect(\App::$cur->users->config['loginUrl'][\App::$cur->type], 'Вы успешно зарегистрировались через Facebook', 'success');
            } else {
                \Inji\Tools::redirect('/users/cabinet/profile', 'Вы успешно зарегистрировались через Facebook', 'success');
            }
        }
    }

}
