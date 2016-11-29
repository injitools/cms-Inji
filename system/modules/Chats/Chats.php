<?php

/**
 * Chats
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class Chats extends Module {

    public function init() {
        App::$primary->view->customAsset('js', '/moduleAsset/Chats/js/chat.js');
    }

    public function getMembers($chatId) {
        $members = \Chats\Chat\Member::getList(['where' => ['chat_id', $chatId]]);
        foreach ($members as $key => $member) {
            if (strtotime($member->date_last_active) - time() > 30) {
                $member->delete();
                unset($members[$key]);
            }
        }
        return $members;
    }

}
