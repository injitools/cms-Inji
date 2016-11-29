<div class="money">
    <div class="content">
        <p>
            Чтобы совершить оплату, переведите сумму <br/>
        </p>
        <big><b><?= number_format($sum, 2, '.', ' '); ?> <?= $method['currency']->acronym(); ?></b></big>
        <br />
        <br />
        <p>
            Одним из удобных вам способов:
        </p>
        <br />
        <?php
        $config = \Money\MerchantHelper\Primary::getConfig();
        echo $config['text'];
        ?>
    </div>
</div>