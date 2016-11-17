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

class Vk extends \Users\SocialHelper {

  public static function auth() {
    $config = static::getConfig();
    if (empty($_GET['code']) && empty($_GET['error'])) {
      $query = [
          'client_id' => $config['appId'],
          'scope' => 'email',
          'response_type' => 'code',
          'display' => 'page',
          'redirect_uri' => 'http://' . INJI_DOMAIN_NAME . '/users/social/auth/vk'
      ];
      \Tools::redirect("https://oauth.vk.com/authorize?" . http_build_query($query));
    }
    if (empty($_GET['code']) && !empty($_GET['error'])) {
      \Tools::redirect('/', 'Произошла ошибка во время авторизации через соц. сеть: ' . $_GET['error_description']);
    }
    $query = [
        'client_id' => $config['appId'],
        'client_secret' => $config['secret'],
        'code' => $_GET['code'],
        'redirect_uri' => 'http://' . INJI_DOMAIN_NAME . '/users/social/auth/vk'
    ];
    $result = @file_get_contents("https://oauth.vk.com/access_token?" . http_build_query($query));
    if ($result === false) {
      \Tools::redirect('/', 'Во время авторизации произошли ошибки', 'danger');
    }
    $result = json_decode($result, true);
    if (empty($result['user_id'])) {
      \Tools::redirect('/', 'Во время авторизации произошли ошибки', 'danger');
    }
    $userQuery = [
        'user_id' => $result['user_id'],
        'fields' => 'sex, bdate, photo_max_orig, home_town',
        'access_token' => $result['access_token']
    ];
    $userResult = @file_get_contents("https://api.vk.com/method/users.get?" . http_build_query($userQuery));
    if (!$userResult) {
      \Tools::redirect('/', 'Во время авторизации произошли ошибки', 'danger');
    }
    $userDetail = json_decode($userResult, true);
    if (empty($userDetail['response'][0])) {
      \Tools::redirect('/', 'Во время авторизации произошли ошибки', 'danger');
    }
    $social = static::getObject();
    $userSocial = \Users\User\Social::get([['uid', $result['user_id']], ['social_id', $social->id]]);
    if ($userSocial && $userSocial->user) {
      \App::$cur->users->newSession($userSocial->user);
      if (!empty(\App::$cur->users->config['loginUrl'][\App::$cur->type])) {
        \Tools::redirect(\App::$cur->users->config['loginUrl'][\App::$cur->type]);
      }
    } else {
      if ($userSocial && !$userSocial->user) {
        $userSocial->delete();
      }
      if (!\Users\User::$cur->id) {
        $user = false;
        if (!empty($result['email'])) {
          $user = \Users\User::get($result['email'], 'mail');
        }
        if (!$user) {
          $user = new \Users\User();
          $user->group_id = 2;
          $user->role_id = 2;
          if (!empty($result['email'])) {
            $user->login = $user->mail = $result['email'];
          }
          $invite_code = (!empty($_POST['invite_code']) ? $_POST['invite_code'] : ((!empty($_COOKIE['invite_code']) ? $_COOKIE['invite_code'] : ((!empty($_GET['invite_code']) ? $_GET['invite_code'] : '')))));
          if (!empty($invite_code)) {
            $invite = \Users\User\Invite::get($invite_code, 'code');
            $inveiteError = false;
            if (!$invite) {
              Msg::add('Такой код пришлашения не найден', 'danger');
              $inveiteError = true;
            }
            if ($invite->limit && !($invite->limit - $invite->count)) {
              Msg::add('Лимит приглашений для данного кода исчерпан', 'danger');
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
      if (!$user->info->photo_file_id && !empty($userDetail['response'][0]['photo_max_orig'])) {
        $user->info->photo_file_id = \App::$cur->files->uploadFromUrl($userDetail['response'][0]['photo_max_orig']);
      }
      if (!$user->info->first_name && !empty($userDetail['response'][0]['first_name'])) {
        $user->info->first_name = $userDetail['response'][0]['first_name'];
      }
      if (!$user->info->last_name && !empty($userDetail['response'][0]['last_name'])) {
        $user->info->last_name = $userDetail['response'][0]['last_name'];
      }
      if (!$user->info->city && !empty($userDetail['response'][0]['home_town'])) {
        $user->info->city = $userDetail['response'][0]['home_town'];
      }
      if (!$user->info->sex && !empty($userDetail['response'][0]['sex'])) {
        $user->info->sex = $userDetail['response'][0]['sex'] == 2 ? 1 : ($userDetail['response'][0]['sex'] == 1 ? 2 : 0);
      }
      if ($user->info->bday == '0000-00-00' && !empty($userDetail['response'][0]['bdate'])) {
        $user->info->bday = substr_count($userDetail['response'][0]['bdate'], '.') == 2 ? \DateTime::createFromFormat('d.m.Y', $userDetail['response'][0]['bdate'])->format('Y-m-d') : (substr_count($userDetail['response'][0]['bdate'], '.') == 1 ? \DateTime::createFromFormat('d.m', $userDetail['response'][0]['bdate'])->format('Y-m-1') : '0000-00-00');
      }
      $user->info->save();
      $userSocial = new \Users\User\Social();
      $userSocial->uid = $result['user_id'];
      $userSocial->social_id = $social->id;
      $userSocial->user_id = $user->id;
      $userSocial->save();
      \App::$cur->users->newSession($user);
      \Tools::redirect(\App::$cur->users->config['loginUrl'][\App::$cur->type], 'Вы успешно зарегистрировались через ВКонтакте', 'success');
    }
  }

  public static function checkAppAccess() {
    $viewer_id = filter_input(INPUT_GET, 'viewer_id', FILTER_SANITIZE_NUMBER_INT);
    $get_auth_key = filter_input(INPUT_GET, 'auth_key', FILTER_SANITIZE_STRING);

    $config = static::getConfig();

    $auth_key = md5($config['appId'] . '_' . $viewer_id . '_' . $config['secret']);

    if ($auth_key !== $get_auth_key) {
      return FALSE;
    }
    $userQuery = [
        'user_id' => $viewer_id,
        'fields' => 'photo_medium,nickname, domain, sex, bdate, city, country, timezone, photo_50, photo_100, photo_200_orig, has_mobile, contacts, education, online, relation, last_seen, status, can_write_private_message, can_see_all_posts, can_post, universities',
        'access_token' => filter_input(INPUT_GET, 'access_token', FILTER_SANITIZE_STRING)
    ];
    $userResult = json_decode(@file_get_contents("https://api.vk.com/method/users.get?" . http_build_query($userQuery)), true);
    $object = static::getObject();

    $socUser = \Users\User\Social::get([['social_id', $object->pk()], ['uid', $viewer_id]]);
    if (!$socUser) {
      $user = new \Users\User();
      $user->login = $userResult['response'][0]['domain'];
      $user->save();
      $info = new \Users\User\Info();
      $info->user_id = $user->id;
      $info->sex = $userResult['response'][0]['sex'] == 2 ? 1 : ($userResult['response'][0]['sex'] == 1 ? 2 : 0);
      $info->first_name = $userResult['response'][0]['first_name'];
      $info->last_name = $userResult['response'][0]['last_name'];
      $info->photo_file_id = \App::$cur->files->uploadFromUrl($userResult['response'][0]['photo_200_orig'], ['accept_group' => 'image']);
      $info->save();
      $social = new \Users\User\Social();
      $social->user_id = $user->id;
      $social->social_id = 1;
      $social->uid = $userResult['response'][0]['uid'];
      $social->save();
    } else {
      $user = $socUser->user;
    }
    return $user;
  }

}
