<?php
echo Html::el('div', [
    'id' => $dataManager->managerId,
    'class' => 'dataManager',
    'data-params' => $params,
    'data-modelname' => ($model ? get_class($model) : $dataManager->modelName) . ($model && $model->pk() ? ':' . $model->pk() : ''),
    'data-managername' => $dataManager->managerName,
    'data-cols' => $dataManager->cols,
    'data-options' => array_merge($dataManager->managerOptions, ['actions' => $dataManager->getActions()])
        ], '', true);
$buttons = $dataManager->getButtons($params, $model);
if ($dataManager->name || $buttons) {
    ?>
    <h3 class="dataManager-title"><?= $dataManager->name; ?> 
      <?php if ($buttons) { ?>
            <div class ='pull-right dataManager-managerButtons'>
                <div class="btn-group">
                  <?php $this->widget('Ui\DataManager/managerButtons', ['buttons' => $buttons]); ?>
                </div>
            </div>
            <div class="clearfix"></div>
        <?php } ?>
    </h3>
    <?php
}
$mainCol = [
    'class' => 'mainTableWrap table-responsive',
    'style' => ''
];
if (!empty($dataManager->managerOptions['categorys'])) {
    $mainCol['style'] .= 'margin-left:260px;';
    echo '<div class ="pull-left dataManager-categorys" style = "width:250px;">';
    $this->widget('Ui\DataManager/categorys', compact('dataManager'));
    echo '</div>';
}
if (empty($params['noFilters']) && !empty($dataManager->managerOptions['filters'])) {
    ?>
    <div class="modal fade" id = "<?= $dataManager->managerId; ?>_filters" >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Фильтры</h3>
                </div>
                <div class="modal-body">
                    <div class ="dataManager-filters">
                      <?php $this->widget('Ui\DataManager/filters', compact('dataManager', 'params')); ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>
    <?php
}
echo Html::el('div', $mainCol, '', true);
$table->draw();
?>
<div class="dataManager-bottomFloat-container">
    <div class="dataManager-bottomFloat">
        <div class="summary"></div>
        <div class="clearfix"></div>
        <div class="pagesContainer pull-right"></div>
        <div class="pull-left">
            Скачать: <a onclick="inji.Ui.dataManagers.get(this).load({download: true}); return false;" href="#">csv</a>
        </div>

        <div class="clearfix"></div>
    </div>
</div>
</div>
<div class="clearfix"></div>
</div>
