<div class="money">
    <div class="content">
        <h2>Конвертация валют</h2>
        <?php
        $form = new Ui\Form();
        $form->method = 'GET';
        $form->begin();
        ?>
        <div class="row">
            <div class="col-sm-6"><?php $form->input('select', 'currency_id', 'Валюта которую отдадите', ['values' => ['' => 'Выберите'] + $wallets, 'value' => !empty($_GET['currency_id']) ? $_GET['currency_id'] : 0]); ?></div>
            <div class="col-sm-6"><?php $form->input('select', 'target_currency_id', 'Валюта которую получите', ['values' => ['' => 'Выберите'] + $wallets, 'value' => !empty($_GET['target_currency_id']) ? $_GET['target_currency_id'] : 0]); ?></div>
        </div>
        <?php
        $form->end('Выбрать');
        foreach ($rates as $rate) {
            $form = new Ui\Form();
            $form->id = 'form_'.Tools::randomString();
            $form->method = "GET";
            $form->begin();
            ?>
            <h3><?= $rate->currency->name(); ?> -> <?= $rate->targetCurrency->name(); ?></h3>
            <div class="row">
                <div class="col-sm-6">
                  <?php $form->input('text', 'exchange[give][amount]', 'Отдадите'); ?>
                </div>
                <div class = "col-sm-6">
                    <?php $form->input('text', 'exchange[get][amount]', 'Получите'); ?>
                </div>
            </div>
            <?php
            $form->input('hidden', 'currency_id', '', ['value' => $rate->currency->id]);
            $form->input('hidden', 'target_currency_id', '', ['value' => $rate->targetCurrency->id]);
            $form->input('hidden', 'exchange[rate_id]', '', ['value' => $rate->id]);
            $form->end('Обменять');
            ?>
            <script>
                inji.onLoad(function () {
                  $('#<?= $form->id; ?> input[name="exchange[give][amount]"]').keyup(function () {
                    $('#<?= $form->id; ?> input[name="exchange[get][amount]"]').val(($(this).val() *<?= $rate->rate; ?>).toFixed(4));
                  });
                  $('#<?= $form->id; ?> input[name="exchange[get][amount]"]').keyup(function () {
                    $('#<?= $form->id; ?> input[name="exchange[give][amount]"]').val(($(this).val() /<?= $rate->rate; ?>).toFixed(4));
                  });
                  $('#<?= $form->id; ?> input[name="exchange[give][amount]"]').change(function () {
                    $('#<?= $form->id; ?> input[name="exchange[get][amount]"]').val(($(this).val() *<?= $rate->rate; ?>).toFixed(4));
                  });
                  $('#<?= $form->id; ?> input[name="exchange[get][amount]"]').change(function () {
                    $('#<?= $form->id; ?> input[name="exchange[give][amount]"]').val(($(this).val() /<?= $rate->rate; ?>).toFixed(4));
                  });
                });
            </script>
            <?php
        }
        ?>
    </div>
</div>