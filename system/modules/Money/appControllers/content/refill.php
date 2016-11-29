<div class="money">
    <div class="content">
        <h2>Пополнение кошелька</h2>
        <?php
        $form = new Ui\Form();
        $form->begin();
        ?>
        <div class="row">
            <div class="col-sm-6"><?php $form->input('select', 'currency_id', 'Кошелек', ['values' => ['' => 'Выберите'] + $currencies, 'value' => !empty($_GET['currency_id']) ? $_GET['currency_id'] : 0]); ?></div>
            <div class="col-sm-6"><?php $form->input('text', 'amount', 'Сумма'); ?></div>
        </div>
        <?php
        $form->end();
        ?>
    </div>
</div>