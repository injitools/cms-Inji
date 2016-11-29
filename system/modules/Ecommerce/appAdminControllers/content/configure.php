<h1>Настройки магазина</h1>
<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Настройки</a></li>
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
      $form->input('checkbox', 'config[view_empty_warehouse]', 'Показывать отсутствующие товары', ['value' => App::$cur->ecommerce->config['view_empty_warehouse']]);
      $form->input('checkbox', 'config[view_empty_image]', 'Показывать товары без изображения', ['value' => App::$cur->ecommerce->config['view_empty_image']]);
      $form->input('checkbox', 'config[sell_empty_warehouse]', 'Продавать отсутствующие товары', ['value' => App::$cur->ecommerce->config['sell_empty_warehouse']]);
      $form->input('checkbox', 'config[sell_over_warehouse]', 'Продавать сверх остатоков на складе', ['value' => App::$cur->ecommerce->config['sell_over_warehouse']]);
      $form->input('checkbox', 'config[show_zero_price]', 'Показывать товары с нулевой ценой', ['value' => App::$cur->ecommerce->config['show_zero_price']]);
      $form->input('checkbox', 'config[show_without_price]', 'Показывать товары без цен', ['value' => App::$cur->ecommerce->config['show_without_price']]);
      $form->input('select', 'config[defaultCategoryView]', 'Стандартный вид категории', ['value' => App::$cur->ecommerce->config['defaultCategoryView'], 'values' => App::$cur->ecommerce->viewsCategoryList()]);
      if (App::$cur->money) {
          $form->input('select', 'config[defaultCurrency]', 'Валюта по умолчанию', ['value' => App::$cur->ecommerce->config['defaultCurrency'], 'values' => ['' => 'Выберите'] + \Money\Currency::getList()]);
      }
      $form->input('text', 'config[orderPrefix]', 'Префикс для номеров заказов', ['value' => App::$cur->ecommerce->config['orderPrefix']]);
      $form->input('text', 'config[notify_mail]', 'E-mail оповещений о новых заказах', ['value' => App::$cur->ecommerce->config['notify_mail']]);
      $form->input('hidden', 'config[save]', '', ['value' => 1]);
      $form->end('Сохранить');
      ?>
        <h3>Обслужвание</h3>
        <a href="/admin/ecommerce/reSearchIndex" class="btn btn-primary">Обновить поисковые индексы</a>
        <h3>Уведомления в браузере</h3>
        <a id ="push-notifications-button" href="#" class="btn btn-primary">Получать уведомления</a>
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


