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

class Twitter extends \Users\SocialHelper {

    private static function requestToken() {
        $config = static::getConfig();
        $oauthNonce = md5(uniqid(rand(), true));
        $oauthTimestamp = time();
        //string
        $oauth_base_text = "GET&";
        $oauth_base_text .= urlencode('https://api.twitter.com/oauth/request_token') . "&";
        $oauth_base_text .= urlencode("oauth_callback=" . urlencode('http://' . INJI_DOMAIN_NAME . '/users/social/auth/twitter') . "&");
        $oauth_base_text .= urlencode("oauth_consumer_key=" . $config['consumer_key'] . "&");
        $oauth_base_text .= urlencode("oauth_nonce=" . $oauthNonce . "&");
        $oauth_base_text .= urlencode("oauth_signature_method=HMAC-SHA1&");
        $oauth_base_text .= urlencode("oauth_timestamp=" . $oauthTimestamp . "&");
        $oauth_base_text .= urlencode("oauth_version=1.0");
        $oauthSignature = base64_encode(hash_hmac("sha1", $oauth_base_text, $config['consumer_secret'] . "&", true));
        //request
        $url = 'https://api.twitter.com/oauth/request_token';
        $url .= '?oauth_callback=' . urlencode('http://' . INJI_DOMAIN_NAME . '/users/social/auth/twitter');
        $url .= '&oauth_consumer_key=' . $config['consumer_key'];
        $url .= '&oauth_nonce=' . $oauthNonce;
        $url .= '&oauth_signature=' . urlencode($oauthSignature);
        $url .= '&oauth_signature_method=HMAC-SHA1';
        $url .= '&oauth_timestamp=' . $oauthTimestamp;
        $url .= '&oauth_version=1.0';
        $response = file_get_contents($url);
        parse_str($response, $result);
        return $result;
    }

    private static function verify() {
        $config = static::getConfig();
        $oauthNonce = md5(uniqid(rand(), true));
        $oauthTimestamp = time();
        $oauth_token = $_GET['oauth_token'];
        $oauth_verifier = $_GET['oauth_verifier'];
        $oauth_token_secret = $_SESSION['oauth_token_secret'];
        //string
        $oauth_base_text = "GET&";
        $oauth_base_text .= urlencode('https://api.twitter.com/oauth/access_token') . "&";
        $oauth_base_text .= urlencode("oauth_consumer_key=" . $config['consumer_key'] . "&");
        $oauth_base_text .= urlencode("oauth_nonce=" . $oauthNonce . "&");
        $oauth_base_text .= urlencode("oauth_signature_method=HMAC-SHA1&");
        $oauth_base_text .= urlencode("oauth_token=" . $oauth_token . "&");
        $oauth_base_text .= urlencode("oauth_timestamp=" . $oauthTimestamp . "&");
        $oauth_base_text .= urlencode("oauth_verifier=" . $oauth_verifier . "&");
        $oauth_base_text .= urlencode("oauth_version=1.0");

        $key = $config['consumer_secret'] . "&" . $oauth_token_secret;
        //request
        $oauth_signature = base64_encode(hash_hmac("sha1", $oauth_base_text, $key, true));
        $url = 'https://api.twitter.com/oauth/access_token';
        $url .= '?oauth_nonce=' . $oauthNonce;
        $url .= '&oauth_signature_method=HMAC-SHA1';
        $url .= '&oauth_timestamp=' . $oauthTimestamp;
        $url .= '&oauth_consumer_key=' . $config['consumer_key'];
        $url .= '&oauth_token=' . urlencode($oauth_token);
        $url .= '&oauth_verifier=' . urlencode($oauth_verifier);
        $url .= '&oauth_signature=' . urlencode($oauth_signature);
        $url .= '&oauth_version=1.0';


        $response = file_get_contents($url);
        parse_str($response, $result);
        return $result;
    }

    private static function getInfo($result) {
        $config = static::getConfig();
        $oauth_nonce = md5(uniqid(rand(), true));
        $oauth_timestamp = time();

        $oauth_token = $result['oauth_token'];
        $oauth_token_secret = $result['oauth_token_secret'];
        $screen_name = $result['screen_name'];

        $oauth_base_text = "GET&";
        $oauth_base_text .= urlencode('https://api.twitter.com/1.1/users/show.json') . '&';
        $oauth_base_text .= urlencode('oauth_consumer_key=' . $config['consumer_key'] . '&');
        $oauth_base_text .= urlencode('oauth_nonce=' . $oauth_nonce . '&');
        $oauth_base_text .= urlencode('oauth_signature_method=HMAC-SHA1&');
        $oauth_base_text .= urlencode('oauth_timestamp=' . $oauth_timestamp . "&");
        $oauth_base_text .= urlencode('oauth_token=' . $oauth_token . "&");
        $oauth_base_text .= urlencode('oauth_version=1.0&');
        $oauth_base_text .= urlencode('screen_name=' . $screen_name);

        $key = $config['consumer_secret'] . '&' . $oauth_token_secret;
        $signature = base64_encode(hash_hmac("sha1", $oauth_base_text, $key, true));


        $url = 'https://api.twitter.com/1.1/users/show.json';
        $url .= '?oauth_consumer_key=' . $config['consumer_key'];
        $url .= '&oauth_nonce=' . $oauth_nonce;
        $url .= '&oauth_signature=' . urlencode($signature);
        $url .= '&oauth_signature_method=HMAC-SHA1';
        $url .= '&oauth_timestamp=' . $oauth_timestamp;
        $url .= '&oauth_token=' . urlencode($oauth_token);
        $url .= '&oauth_version=1.0';
        $url .= '&screen_name=' . $screen_name;

        $response = file_get_contents($url);

        return json_decode($response, true);
    }

    public static function auth() {
        if (empty($_GET['oauth_verifier']) || empty($_SESSION['oauth_token_secret'])) {
            $tokens = self::requestToken();
            $_SESSION['oauth_token_secret'] = $tokens['oauth_token_secret'];
            \Tools::redirect("https://api.twitter.com/oauth/authorize?oauth_token={$tokens['oauth_token']}");
        }
        $verify = static::verify();

        if (!$verify['user_id']) {
            \Tools::redirect('/', 'Не удалось авторизоваться через twitter');
        }
        $userDetail = static::getInfo($verify);

        $social = static::getObject();
        $userSocial = \Users\User\Social::get([['uid', $userDetail['id']], ['social_id', $social->id]]);
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
                $user = new \Users\User();
                $user->group_id = 2;
                $user->role_id = 2;
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
            } else {
                $user = \Users\User::$cur;
            }
            $name = explode(' ', $userDetail['name']);
            $user->info->first_name = $name[0];
            $user->info->last_name = $name[1];
            $user->info->city = $userDetail['location'];
            $user->info->save();
            $userSocial = new \Users\User\Social();
            $userSocial->uid = $userDetail['id'];
            $userSocial->social_id = $social->id;
            $userSocial->user_id = $user->id;
            $userSocial->save();
            \App::$cur->users->newSession($user);
            if (!empty(\App::$cur->users->config['loginUrl'][\App::$cur->type])) {
                \Tools::redirect(\App::$cur->users->config['loginUrl'][\App::$cur->type], 'Вы успешно зарегистрировались через Twitter', 'success');
            } else {
                \Tools::redirect('/users/cabinet/profile', 'Вы успешно зарегистрировались через Twitter', 'success');
            }
        }
    }

}
