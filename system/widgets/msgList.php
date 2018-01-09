<?php
foreach (\Inji\Msg::get() as $msg) {
    ?>
    <div class="alert alert-<?= $msg['status']; ?> alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <strong><?php
            switch ($msg['status']) {
                case 'success':
                    echo 'Успех!';
                    break;
                case 'danger':
                case 'warning':
                    echo 'Внимание!';
                    break;
                default:
                    echo 'Информация.';
            }
            ?></strong> <?= $msg['text']; ?>
    </div>
    <?php
}
\Inji\Msg::flush();
