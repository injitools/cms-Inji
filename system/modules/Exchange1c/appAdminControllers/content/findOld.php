<h2>Объекты миграции не обновляемые при обмене</h2>
<form method="post"
      onsubmit="return confirm('Вместе с объектами могут быть удалены их дочерние зависимости. Вы уверены?')">
    <?php
    $date = new DateTime();
    $date->sub(new DateInterval('P3D'));
    echo '<table class="table table-striped table-hovered">';
    echo '<tr><th><input onchange="var checked = this.checked;$.each($(\'.forDelete\'),function(){this.checked=checked})" type="checkbox"/></th><th>id на сайте</th><th>Название объекта</th><th>Объект</th><th>Последняя выгрузка</th><th>Первая выгрузка</th><th>Id в 1с</th></tr>';
    foreach (\Migrations\Id::getList(['where' => ['last_access', $date->format('Y-m-d H:i:s'), '<'], 'order' => ['last_access', 'asc']]) as $id) {
        $modelName = $id->type;
        $model = $modelName::get($id->object_id);
        $name = $model ? "<a href='/admin/{$model->genViewLink()}'>{$model->name()}</a>" : 'Нет в базе';
        ?>
        <tr>
            <td><input class="forDelete" type="checkbox" name="ids[]" value="<?= $id->id; ?>"/></td>
            <td><?= $id->object_id; ?></td>
            <td>
                <a href="/admin/<?= substr($modelName, 0, strpos($modelName, '\\')); ?>/<?= urlencode(substr($modelName, strpos($modelName, '\\') + 1)); ?>"><?= $modelName::$objectName ? $modelName::$objectName : ''; ?></a>
            </td>
            <td><?= $name; ?></td>
            <td><?= $id->last_access; ?></td>
            <td><?= $id->date_create; ?></td>
            <td><?= $id->parse_id; ?></td>
        </tr>
        <?php
    }
    echo '</table>';
    ?>
    <button class="btn btn-primary btn-lg">Удалить</button>
</form>
