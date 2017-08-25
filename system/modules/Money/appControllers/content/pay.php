<div class="money">
    <div class="content">
        <h2>Оплата счета №<?= $pay->id; ?></h2>
        <h1>К оплате: <b><?= $pay->sum; ?> <?= $pay->currency->acronym(); ?></b></h1>
        <h3>Выберите удобный способ оплаты</h3>
        <div class="row">
            <?php
            $linlcount = 0;
            $lastlink = '';
            foreach ($merchants as $merchant) {
                $allowCurrencies = $merchant->allowCurrencies($pay);
                if (!$allowCurrencies) {
                    continue;
                }
                ?>
                <div class="col-md-4 col-sm-6 col-lg-3 text-center">
                    <h4><?= $merchant->name(); ?></h4>
                    <?php if ($merchant->image) { ?>
                        <img src="<?= Statics::file($merchant->image->path, '150x150'); ?>"
                             class="img-responsive"/>
                    <?php } ?>
                    <?php
                    foreach ($allowCurrencies as $allowCurrency) {
                        $lastlink = "/money/merchants/go/{$pay->id}/{$merchant->id}/{$allowCurrency['currency']->id}";
                        $linlcount++;
                        $className = 'Money\MerchantHelper\\' . $merchant->object_name;
                        $sum = $className::getFinalSum($pay, $allowCurrency);
                        ?>
                        <a class="btn btn-primary btn-lg"
                           href="/money/merchants/go/<?= $pay->id; ?>/<?= $merchant->id; ?>/<?= $allowCurrency['currency']->id; ?>">Оплатить <?= $sum; ?> <?= $allowCurrency['currency']->acronym(); ?></a>
                        <?php
                    }
                    ?>
                    <?= $merchant->previewImage ? '<img src="' . $merchant->previewImage->path . '" class="img-responsive" />' : ''; ?>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</div>