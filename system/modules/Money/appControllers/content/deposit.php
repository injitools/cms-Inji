<div class="money">
    <div class="content">
        <div class="panel panel-default">
            <div class="panel-heading">
                <?= I18n\Text::module('Money', 'Пополнение счета'); ?> <?= $currency ? $currency->name . ' (' . $currency->code . ')' : ''; ?>
            </div>
            <div class="panel-body">
                <?php
                $where = [['active', 1], ['deposit', 1]];
                if ($currency) {
                    $where = [['currencies:currency_id', $currency->id]];
                }
                $merchants = \Money\Merchant::getList(['where' => $where]);
                $i=0;
                foreach ($merchants as $merchant) {
                    if($i++){
                        echo '<hr />';
                    }
                    echo "<h2>{$merchant->name}</h2>";
                    $helper = class_exists('\Money\MerchantHelper\\' . $merchant->object_name) ? '\Money\MerchantHelper\\' . $merchant->object_name : $merchant->object_name;
                    $helper::showDepositForm(!$currency ? 0 : $currency->id);
                }
                ?>
            </div>
        </div>
    </div>
</div>