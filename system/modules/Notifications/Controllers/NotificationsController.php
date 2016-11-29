<?php

/**
 * Notifications controller
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class NotificationsController extends \Controller {

    public function checkAction() {
        $result = new Server\Result();
        $device = $this->Notifications->getCurDevice();
        $subscriber = $this->Notifications->getCurSubscriber();
        $subscribes = Notifications\Subscribe::getList(['where' => ['subscriber_id', $subscriber->id]]);
        $chanelsIds = [];
        foreach ($subscribes as $subscribe) {
            $chanelsIds[$subscribe->chanel_id] = $subscribe->chanel_id;
        }
        $result->content = [];
        if ($chanelsIds) {
            $notifications = Notifications\Notification::getList(['where' => [
                            ['date_create', $device->date_last_check, '>='],
                            ['chanel_id', implode(',', $chanelsIds), 'IN'],
            ]]);
            foreach ($notifications as $notification) {
                $result->content[] = $notification->_params;
            }
        }
        $device->date_last_check = date('Y-m-d H:i:s');
        $device->save();
        $result->send();
    }

}
