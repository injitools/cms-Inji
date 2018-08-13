<?php
$form = new Ui\Form();
$form->begin();
?>
    <div class="row">
        <div class="col-sm-6"><?php

            $currencies = $helper::getMerchant()->currencies;
            $curs = [];
            foreach ($currencies as $currency) {
                if ($currency->currency->deposit) {
                    $curs[$currency->id] = $currency->currency->name;
                }
            }
            $cur = 0;
            if ($_GET['currency_id']) {
                $cur = $_GET['currency_id'];
            }
            if ($currencyId) {
                $cur = $currencyId;
            }
            $form->input('select', 'currency_id', 'Валюта', ['values' => ['' => 'Выберите'] + $curs, 'value' => $cur]);

            ?></div>
        <div class="col-sm-6"><?php $form->input('text', 'amount', 'Сумма'); ?></div>
    </div>
<?php
$form->end();
?>