<?php

/**
 * Notifications module
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class Notifications extends Module {

    public function init() {
        if (\Notifications\Subscribe::get($this->getCurSubscriber()->id, 'subscriber_id')) {
            App::$cur->view->customAsset('js', '/moduleAsset/Notifications/js/Notifications.js');
        }
    }

    public function subscribe($chanelAlias) {
        $chanel = $this->getChanel($chanelAlias);
        $subscriber = $this->getCurSubscriber();
        $subscribe = Notifications\Subscribe::get([['subscriber_id', $subscriber->id], ['chanel_id', $chanel->id]]);
        if ($subscribe) {
            $response = new Server\Result();
            $response->successMsg = 'Вы уже подписаны';
            $response->send();
        }
        $subscribe = new Notifications\Subscribe();
        $subscribe->subscriber_id = $subscriber->id;
        $subscribe->chanel_id = $chanel->id;
        $subscribe->save();
        $response = new Server\Result();
        $response->successMsg = 'Вы были подписаны на уведомления';
        $response->send();
    }

    public function getChanel($alias) {
        $chanel = \Notifications\Chanel::get($alias, 'alias');
        if (!$chanel) {
            $chanel = new \Notifications\Chanel();
            $chanel->alias = $alias;
            $chanel->name = $alias;
            $chanel->save();
        }
        return $chanel;
    }

    public function getCurSubscriber() {
        $device = $this->getCurDevice();
        if (!$device->subscriber) {
            $subscriber = null;
            if (Users\User::$cur->id) {
                $subscriber = \Notifications\Subscriber::get(Users\User::$cur->id, 'user_id');
            }
            if (!$subscriber) {
                $subscriber = new \Notifications\Subscriber();
                if (Users\User::$cur->id) {
                    $subscriber->user_id = Users\User::$cur->id;
                }
                $subscriber->save();
            }
            $device->subscriber_id = $subscriber->id;
            $device->save();
            return $subscriber;
        }

        return $device->subscriber;
    }

    public function getCurDevice() {
        if (empty($_COOKIE['notification-device'])) {
            $deviceKey = Tools::randomString(70);
            setcookie("notification-device", $deviceKey, time() + 360000, "/");
        } else {
            $deviceKey = $_COOKIE['notification-device'];
            setcookie("notification-device", $_COOKIE['notification-device'], time() + 360000, "/");
        }
        $device = \Notifications\Subscriber\Device::get($deviceKey, 'key');
        if (!$device) {
            $device = new \Notifications\Subscriber\Device();
            $device->key = $deviceKey;
            $device->save();
            $device->date_last_check = $device->date_create;
            $device->save();
        }
        return $device;
    }

}
