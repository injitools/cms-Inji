<div class="money">
    <div class="content">
        <?php
        $table = new \Ui\Table();
        $table->name = 'Ваши счета';
        $table->setCols([
            '№',
            'Описание',
            'Сумма',
            'Валюта',
            '',
            ''
        ]);
        foreach ($pays as $pay) {
            $table->addRow([
                $pay->id,
                $pay->description,
                $pay->sum,
                $pay->currency->name(),
                '<a href = "/money/merchants/pay/' . $pay->id . '" class="btn btn-success btn-sm">Оплатить</a>',
                '<a href = "/money/merchants/cancelPay/' . $pay->id . '" class="btn btn-danger btn-sm">Отказаться</a>'
            ]);
        }
        $table->draw();
        ?>
    </div>
</div>