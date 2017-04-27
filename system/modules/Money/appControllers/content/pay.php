<div class="money">
    <div class="content">
        <h2>Оплата счета №<?= $pay->id; ?></h2>
        <h1 class="text-center">К оплате: <b><?= $pay->sum; ?> <?= $pay->currency->acronym(); ?></b></h1>
        <h3>Выберите удобный способ оплаты и валюту</h3>
        <table class="table table-striped table-bordered">
            <tr>
                <th>Способ оплаты</th>
                <th>Валюта</th>
                <th></th>
            </tr>
            <?php
            $linlcount = 0;
            $lastlink = '';
            foreach ($merchants as $merchant) {
                $allowCurrencies = $merchant->allowCurrencies($pay);
                if (!$allowCurrencies) {
                    continue;
                }
                ?>
                <tr>
                    <td>
                        <img src="<?= Statics::file($merchant->image ? $merchant->image->path : '/static/system/images/no-image.png', '150x150'); ?>"
                             class="img-responsive"/>
                        <h4 class="text-center"><?= $merchant->name(); ?></h4>
                    </td>
                    <td>
                        <?php
                        foreach ($allowCurrencies as $allowCurrency) {
                            $lastlink = "/money/merchants/go/{$pay->id}/{$merchant->id}/{$allowCurrency['currency']->id}";
                            $linlcount++;
                            $className = 'Money\MerchantHelper\\' . $merchant->object_name;
                            $sum = $className::getFinalSum($pay, $allowCurrency);
                            ?>
                            <b><?= $allowCurrency['currency']->name(); ?></b>
                            <a class="btn btn-primary"
                               href="/money/merchants/go/<?= $pay->id; ?>/<?= $merchant->id; ?>/<?= $allowCurrency['currency']->id; ?>">Оплатить <?= $sum; ?> <?= $allowCurrency['currency']->acronym(); ?></a>
                            <?php
                        }
                        ?>
                    </td>
                    <td width="100%">
                        <?= $merchant->previewImage ? '<img src="' . $merchant->previewImage->path . '" class="img-responsive" />' : ''; ?>
                    </td>
                </tr>
                <?php
            }
            if($linlcount===1){
                Tools::redirect($lastlink);
            }
            ?>
        </table>
    </div>
</div>