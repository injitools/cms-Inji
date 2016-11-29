<?php
return [
    'widget' => function() {
        ?>
        <div class="panel panel-default">
            <div class="panel-heading">Общие настройки сайта</div>
            <div class="panel-body">
                <p>Название: <?= !empty(App::$primary->config['site']['name']) ? App::$primary->config['site']['name'] : 'Не указано'; ?></p>
                <p>Email: <?= !empty(App::$primary->config['site']['email']) ? App::$primary->config['site']['email'] : 'Не указано'; ?></p>
            </div>
            <div class="panel-footer">
                <a href ="/admin/dashboard/siteConfig">Изменить</a>
            </div>
        </div>
        <?php
    }
];
