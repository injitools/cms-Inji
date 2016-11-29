<?php
return [
    'widget' => function() {
        ?>
        <div class="panel panel-default">
            <div class="panel-heading">Отзывы</div>
            <div class="panel-body">
                <p>Всего: <?= Callbacks\Callback::getCount(); ?></p>
                <p>Новых сегодня: <?= Callbacks\Callback::getCount(['where' => ['date_create', date('Y-m-d 00:00:00'), '>']]); ?></p>
            </div>
            <div class="panel-footer">
                <a href ="/admin/Callbacks/Callback">Просмотр</a>
            </div>
        </div>
        <?php
    }
        ];
        