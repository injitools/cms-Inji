<?php
return [
    'accessCheck' => function() {
        $dataManager = new \Ui\DataManager('Materials\Material');
        return $dataManager->checkAccess();
    },
    'widget' => function() {
        ?>
        <div class="panel panel-default">
            <div class="panel-heading">Материалы</div>
            <div class="panel-body">
                <p>Всего: <?= Materials\Material::getCount(); ?></p>
                <p>Новых сегодня: <?= Materials\Material::getCount(['where' => ['date_create', date('Y-m-d 00:00:00'), '>']]); ?></p>
            </div>
            <div class="panel-footer">
                <a href ="/admin/Materials/Material">Управление</a>
            </div>
        </div>
        <?php
    }];
        