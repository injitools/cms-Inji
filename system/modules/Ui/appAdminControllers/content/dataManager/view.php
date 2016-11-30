<?php
$modelName = get_class($item);
$table = new Ui\Table();
$table->name = $item->name();
$formParams = ['formName' => 'manager'];
$aform = new \Ui\ActiveForm($item, $formParams['formName']);
if ($aform->checkAccess()) {
    $table->addButton([
        'text' => 'Редактировать',
        'onclick' => 'inji.Ui.forms.popUp("' . addcslashes($modelName, '\\') . ':' . $item->pk() . '",' . json_encode($formParams) . ');',
    ]);
}
$row = [];
$cols = !empty($modelName::$views['manager']['cols']) ? $modelName::$views['manager']['cols'] : array_keys($modelName::$cols);
foreach ($cols as $colName) {
    $colInfo = $modelName::getColInfo($colName);
    $type = !empty($colInfo['colParams']['type']) ? $colInfo['colParams']['type'] : 'string';
    if ($type != 'dataManager') {
        $table->addRow([
            !empty($modelName::$labels[$colName]) ? $modelName::$labels[$colName] : $colName,
            \Model::resloveTypeValue($item, $colName, true)
        ]);
    }
}
$table->draw();
$form = new \Ui\Form();
$relations = $modelName::relations();
foreach ($cols as $colName) {
    $colInfo = $modelName::getColInfo($colName);
    if ($colInfo['colParams']['type'] == 'dataManager') {
        $dataManager = new \Ui\DataManager($relations[$colInfo['colParams']['relation']]['model']);
        $dataManager->draw(['relation' => $colInfo['colParams']['relation']], $item);
    }
}
?>
<div>
    <h3>Комментарии (<?=
      \Dashboard\Comment::getCount(['where' => [
              ['item_id', $item->id],
              ['model', $modelName],
      ]]);
      ?>)</h3>
    <?php
    foreach (\Dashboard\Comment::getList(['where' => [
            ['item_id', $item->id],
            ['model', $modelName],
        ], 'order' => ['date_create', 'desc']]) as $comment) {
        ?>
        <div class="row">
            <div class="col-sm-3" style="max-width: 300px;">
                <a href='/admin/Users/view/User/<?= $comment->user->pk(); ?>'><?= $comment->user->name(); ?></a><br />
                <?= $comment->date_create; ?>
            </div>
            <div class="col-sm-9">
                <?= $comment->text; ?>
            </div>
        </div>
        <?php
    }
    ?>
</div>
<div>
  <?php
  $form = new \Ui\Form();
  $form->begin('Оставить комментарий');
  $form->input('textarea', 'comment', 'Комментарий');
  $form->end();
  ?>
</div>
<div>
    <h1>Хронология</h1>
    <?php
    $dataManager = new \Ui\DataManager('Dashboard\Activity');
    $dataManager->draw(['filters' => [
            'item_id' => ['max' => $item->id, 'min' => $item->id],
            'model' => ['compareType' => 'equals', 'value' => $modelName]
    ]]);
    ?>
</div>