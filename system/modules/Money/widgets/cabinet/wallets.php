<h3>Мои кошельки</h3>
<div class="row">
  <?php
    $blocked = App::$cur->money->getUserBlocks();
    $wallets = App::$cur->money->getUserWallets();
    $rates = Money\Currency\ExchangeRate::getList();
    foreach ($wallets as $wallet) {
        ?>
        <div class="col-sm-4">
            <h4><?= $wallet->currency->name(); ?></h4>
            <b><?= $wallet->showAmount(); ?></b> <?= $wallet->currency->acronym(); ?><br />
            <?php
            if (!empty($blocked[$wallet->currency_id])) {
                echo "Заблокировано: {$blocked[$wallet->currency_id]} " . $wallet->currency->acronym() . "<br />";
            }
            if ($wallet->currency->refill) {
                echo "<a href = '/money/refill?currency_id={$wallet->currency_id}'>Пополнить</a> ";
            }
            foreach ($rates as $rate) {
                if ($rate->currency_id == $wallet->currency_id && !empty($wallets[$rate->target_currency_id])) {
                    echo "<a href = '/money/exchange?currency_id={$wallet->currency_id}'>Обменять</a>";
                    break;
                }
            }
            if ($wallet->currency->transfer) {
                echo " <a href ='/money/transfer?currency_id={$wallet->currency_id}'>Перевести</a><br />";
            }
            echo " <a href ='/users/cabinet/walletHistory?currency_id={$wallet->currency_id}'>История</a>";
            echo " <a href ='/users/cabinet/walletBlocked?currency_id={$wallet->currency_id}'>Блокировки</a>";
            ?>
        </div>
        <?php
    }
    ?>
</div>
<?php
$transfers = Money\Transfer::getList(['where' => [
                ['user_id', \Users\User::$cur->id],
                ['complete', 0],
                ['canceled', 0]
        ]]);
if ($transfers) {
    echo "<h3>У вас есть незаконченные переводы</h3>";
    echo "<ul>";
    foreach ($transfers as $transfer) {
        echo "<li><a href = '/money/confirmTransfer/{$transfer->id}'>{$transfer->name()}</a></li>";
    }
    echo "</ul>";
}
$pays = \Money\Pay::getList(['where' => [['pay_status_id', 1], ['user_id', \Users\User::$cur->id]]]);
if ($pays) {
    echo "<h3>У вас есть неоплаченные счета</h3>";
    $this->content(['module' => \App::$cur->money, 'content' => 'pays', 'data' => compact('pays')]);
}