<h4>Категории
    <div class="pull-right">
        <a href="#" role="button" class ='btn btn-xs btn-primary' onclick='inji.Ui.dataManagers.get(this).newCategory();'>Создать</a>
    </div>
</h4>
<?php
$model = $dataManager->managerOptions['categorys']['model'];
?>
<a href='#' onclick='inji.Ui.dataManagers.get(this).switchCategory(this);return false;' data-index='<?= $model::index(); ?>' data-path ='/' data-id='0'> Корень</a>
<div class="categoryTree">
    <?php
    $dataManager->drawCategorys();
    ?>
</div>