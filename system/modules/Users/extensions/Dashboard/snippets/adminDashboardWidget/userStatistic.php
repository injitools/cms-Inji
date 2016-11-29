<?php
return [
    'widget' => function() {
        ?>
        <div class="panel panel-default">
            <div class="panel-heading">Пользователи</div>
            <div class="panel-body">
                <p>Всего: <?= Users\User::getCount(); ?></p>
                <p>Новых сегодня: <?= Users\User::getCount(['where' => ['date_create', date('Y-m-d 00:00:00'), '>']]); ?></p>
            </div>
            <div class="panel-footer">
                <a href ="/admin/users/User">Управление</a>
            </div>
        </div>
        <?php
    }
        ];
        