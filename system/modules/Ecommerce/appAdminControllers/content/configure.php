<h1>Настройки магазина</h1>
<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab"
                                              data-toggle="tab">Настройки</a></li>
    <?php
    $dataManagers = [];
    foreach ($managers as $manager) {
        $dataManager = new Ui\DataManager($manager);
        $dataManagers[$manager] = $dataManager;
        $code = 'tab_' . str_replace('\\', '_', $manager);
        echo "<li role='presentation'><a href='#{$code}' aria-controls='{$code}' role='tab' data-toggle='tab'>{$dataManager->name}</a></li>";
    }
    ?>
</ul>
<div class="tab-content">
    <div role="tabpanel" class="tab-pane fade in active" id="home">
        <?php
        $form = new Ui\Form();
        $form->begin();
        foreach ($options as $option) {
            $form->input($option['type'], "config[{$option['name']}]", $option['label'], $option['options']);
        }
        $form->input('hidden', 'config[save]', '', ['value' => 1]);
        $form->end('Сохранить');
        ?>
        <h3>Обслужвание</h3>
        <a href="/admin/ecommerce/reSearchIndex" class="btn btn-primary">Обновить поисковые индексы</a>
        <a href="/admin/ecommerce/reCalcCategories" class="btn btn-primary">Обновить счетчики товаров в категориях</a>
        <h3>Уведомления в браузере</h3>
        <a id="push-notifications-button" href="#" class="btn btn-primary">Получать уведомления</a>
        <script>
          inji.onLoad(function () {
            document.querySelector('#push-notifications-button').addEventListener('click', function () {
              if (!Notification) {
                alert('Desktop notifications not available in your browser. Try Chromium.');
                return;
              }
              if (Notification.permission !== "granted") {
                Notification.requestPermission();
              }
              inji.Server.request({
                url: '/admin/ecommerce/newOrdersSubscribe'
              });
            });
          })

        </script>
    </div>
    <?php
    foreach ($dataManagers as $manager => $dataManager) {
        $code = 'tab_' . str_replace('\\', '_', $manager);
        ?>
        <div role="tabpanel" class="tab-pane fade" id="<?= $code; ?>">
            <?php
            $dataManager->draw();
            ?>
        </div>
        <?php
    }
    ?>
</div>


