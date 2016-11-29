<?php
return [
    'widget' => function() {
        ?>
        <div class="panel panel-default">
            <div class="panel-heading">Полученные формы</div>
            <div class="panel-body">
                <p>Всего: <?= UserForms\Recive::getCount(); ?></p>
                <p>Новых сегодня: <?= UserForms\Recive::getCount(['where' => ['date_create', date('Y-m-d 00:00:00'), '>']]); ?></p>
            </div>
            <div class="panel-footer">
                <a href ="/admin/UserForms/Recive">Просмотр</a>
            </div>
        </div>
        <?php
    }
        ];
        