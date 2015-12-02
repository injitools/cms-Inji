<?php
return [
    'widget' => function() {
        ?>
        <div class="panel panel-default">
          <div class="panel-heading">Материалы</div>
          <div class="panel-body">
            <p>Всего: <?= Materials\Material::getCount(); ?></p>
            <p>Новых сегодня: <?= Materials\Material::getCount(['where' => ['date_create', date('Y-m-d 00:00:00'), '>']]); ?></p>
          </div>
          <div class="panel-footer">
            <a href ="/admin/Materials/Material">Просмотр</a>
          </div>
        </div>
        <?php
    }
        ];
        