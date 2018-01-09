<div class="row">
    <div class="col-lg-12">
      <?php
        $dataManager = Inji\Ui\DataManager::forModel('Inji\Apps\App', 'setup');
        $dataManager->draw();
        ?>
    </div>
    <div class="col-lg-12">
        <?php
        $dataManager = Inji\Ui\DataManager::forModel('Inji\Db\Options', 'setup');
        $dataManager->draw();
        ?>
    </div>
</div>