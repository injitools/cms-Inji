<?php

/**
 * Chat Controller
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class ChatsController extends Controller {

    public function eventsAction($chatId = 0) {
        $chatId = (int) $chatId;
        $result = new Server\Result();
        if (!$chatId || !($chat = \Chats\Chat::get($chatId))) {
            $result->success = false;
            $result->content = 'Такого чата не существует';
            $result->send();
        }
        $where = [
            ['chat_id', $chatId],
            ['delete', 0]
        ];
        if (!empty($_GET['lastEventDate'])) {
            $where[] = ['date_create', $_GET['lastEventDate'], '>'];
        }
        $result->content = [
            'members' => [],
            'messages' => []
        ];
        if (Users\User::$cur->id) {
            $member = \Chats\Chat\Member::get([['chat_id', $chatId], ['user_id', Users\User::$cur->id]]);
            if (!$member) {
                $member = new \Chats\Chat\Member();
                $member->user_id = Users\User::$cur->id;
                $member->chat_id = $chatId;
            }
            $member->date_last_active = date('Y-m-d H:i:s');
            $member->save();
        }

        $messages = \Chats\Chat\Message::getList(['where' => $where, 'limit' => 20, 'order' => ['date_create', 'DESC']]);
        if ($messages) {
            $messages = array_reverse($messages);
            foreach ($messages as $message) {
                $msg = [
                    'message' => $message->_params,
                    'fullUserName' => $message->user->name(),
                    'userFirstName' => $message->user->info->first_name,
                    'userPhoto' => $message->user->info->photo ? $message->user->info->photo->path : '/static/system/images/no-image.png'
                ];
                $result->content['messages'][] = $msg;
            }
        }
        $result->send();
    }

    function deleteMsgAction($messageId) {
        $result = new Server\Result();
        if (!\Users\User::$cur->isAdmin()) {
            $result->success = false;
            $result->content = 'Вы не админ';
            $result->send();
        }
        $msg = Chats\Chat\Message::get((int) $messageId);
        if (!$msg) {
            $result->success = false;
            $result->content = 'Сообщение не найдено';
            $result->send();
        }
        $msg->delete = 1;
        $msg->save();
        $result->successMsg = 'Сообщение удалено';
        $result->send();
    }

    function banUserAction($messageId) {
        $result = new Server\Result();
        if (!\Users\User::$cur->isAdmin()) {
            $result->success = false;
            $result->content = 'Вы не админ';
            $result->send();
        }
        $msg = Chats\Chat\Message::get((int) $messageId);
        if (!$msg) {
            $result->success = false;
            $result->content = 'Сообщение не найдено';
            $result->send();
        }
        $ban = new Chats\Chat\Ban();
        $ban->user_id = $msg->user_id;
        $ban->chat_id = $msg->chat_id;
        $ban->chat_message_id = $msg->id;
        $ban->save();
        $msgs = Chats\Chat\Message::getList(['where' => ['user_id', $ban->user_id]]);
        foreach ($msgs as $msg) {
            $msg->delete = 1;
            $msg->save();
        }
        $result->content = array_flip(array_keys($msgs));
        $result->successMsg = 'Сообщение удалено';
        $result->send();
    }

    public function sendFormAction($chatId = 0) {
        $chatId = (int) $chatId;
        $result = new Server\Result();
        if (!$chatId || !($chat = \Chats\Chat::get($chatId))) {
            $result->success = false;
            $result->content = 'Такого чата не существует';
            $result->send();
        }
        if (!Users\User::$cur->id) {
            $result->success = false;
            $result->content = 'Вы не авторизованы';
            $result->send();
        }
        if (empty($_POST['chat-message']) || !trim($_POST['chat-message'])) {
            $result->success = false;
            $result->content = 'Сообщение не может быть пустым';
            $result->send();
        }
        if (Chats\Chat\Ban::get(['user_id', \Users\User::$cur->id])) {
            $result->success = false;
            $result->content = 'Вы не можете писать в чат';
            $result->send();
        }
        $message = new \Chats\Chat\Message();
        $message->user_id = Users\User::$cur->id;
        $message->chat_id = $chatId;
        $message->text = htmlspecialchars(trim($_POST['chat-message']));
        $message->save();
        $result->successMsg = 'Ваше сообщение было отправлено';
        $result->send();
    }

}
