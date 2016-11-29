<?php
if (!empty($params[0])) {
    $chatId = $params[0];
}
if (empty($chatId)) {
    echo 'Чат не найден';
    return;
}
$chat = \Chats\Chat::get($chatId);
if (!$chat) {
    echo 'Чат не найден';
    return;
}
$id = 'chat-' . Tools::randomString();
$msgTemplate = '<div class="chats-chat-message"><b title="message-date_create user-fullName">user-firstName</b>: message-text</div>';
$events = $chat->events(['order' => ['date_create', 'DESC'], 'key' => false]);
$lastDate = $events ? $events[0]->date_create : 0;
$inputSize = empty($inputSize) ? 'col-md-9' : $inputSize;
$submitSize = empty($submitSize) ? 'col-md-3' : $submitSize;
?>

<div id ="<?= $id; ?>" class="chats-chat" data-chat-id="<?= $chatId; ?>" data-last-event-date="<?= $lastDate; ?>" data-msg-count="<?= $chat->messages(['count' => true]); ?>">
    <div class ="chats-chat-message-template" style="display: none;">
      <?= $msgTemplate; ?>
    </div>
    <div class="chats-chat-inputarea">
        <form onsubmit="inji.Chats.sendForm(this, '<?= $id; ?>');this.querySelector('[name=\'chat-message\']').value = '';return false;">
            <div class="form-group">
                <div class="row">
                    <div class="<?= $inputSize; ?>">
                        <input class="form-control" name ='chat-message'> 
                    </div>
                    <div class="<?= $submitSize; ?>">
                        <button class="btn btn-success btn-block">Отправить</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="chats-chat-messageList"></div>
</div>
<script>
    inji.onLoad(function () {
      inji.Chats.get('<?= $id; ?>')
    })
</script>