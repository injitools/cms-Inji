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
        $subscriber = $this->getCurSubscriber();
        if ($subscriber && $subscriber->subscribes) {
            App::$cur->view->customAsset('js', '/moduleAsset/Notifications/js/Notifications.js');
        }
    }

    public function subscribe($chanelAlias) {
        $chanel = $this->getChanel($chanelAlias);
        $subscriber = $this->getCurSubscriber(true);
        $subscribe = Notifications\Subscribe::get([['subscriber_id', $subscriber->id], ['chanel_id', $chanel->id]]);
        if ($subscribe) {
            $response = new Server\Result();
            $response->successMsg = 'Вы уже подписаны';
            return $response->send();
        }
        $subscribe = new Notifications\Subscribe();
        $subscribe->subscriber_id = $subscriber->id;
        $subscribe->chanel_id = $chanel->id;
        $subscribe->save();
        $response = new Server\Result();
        $response->successMsg = 'Вы были подписаны на уведомления';
        return $response->send();
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

    public function getCurSubscriber($create = false) {
        $device = $this->getCurDevice($create);
        if (!$device) {
            return false;
        }
        if (!$device->subscriber) {
            $subscriber = null;
            if (class_exists('Users\User') && Users\User::$cur->id) {
                $subscriber = \Notifications\Subscriber::get(Users\User::$cur->id, 'user_id');
            }
            if (!$subscriber) {
                $subscriber = new \Notifications\Subscriber();
                if (class_exists('Users\User') && Users\User::$cur->id) {
                    $subscriber->user_id = Users\User::$cur->id;
                }
                $subscriber->save(['empty' => true]);
            }
            $device->subscriber_id = $subscriber->id;
            $device->save();
            return $subscriber;
        }

        return $device->subscriber;
    }

    public function getCurDevice($create = false) {
        $deviceKey = $this->getDevicekey();
        if (!$deviceKey) {
            if ($create) {
                $deviceKey = Tools::randomString(70);
                $this->setDeviceKey($deviceKey);
            } else {
                return false;
            }
        } else {
            $this->setDeviceKey($deviceKey);
        }
        $device = \Notifications\Subscriber\Device::get($deviceKey, 'key');
        if (!$device && $create) {
            $device = new \Notifications\Subscriber\Device();
            $device->key = $deviceKey;
            $device->save();
            $device->date_last_check = $device->date_create;
            $device->save();
        } elseif (!$device) {
            return false;
        }
        return $device;
    }

    public function getDevicekey() {
        if (!empty($_SESSION['notification-device'])) {
            return $_SESSION['notification-device'];
        }
        if (!empty($_COOKIE['notification-device'])) {
            return $_COOKIE['notification-device'];
        }
    }

    public function setDeviceKey($deviceKey) {
        if (headers_sent()) {
            $_SESSION['notification-device'] = $deviceKey;
        } else {
            setcookie("notification-device", $deviceKey, time() + 360000, "/");
        }
    }
}