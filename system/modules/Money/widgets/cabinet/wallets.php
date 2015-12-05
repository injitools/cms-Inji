<h3>Мои кошельки</h3>
<div class="row">
  <?php
  $wallets = App::$cur->money->getUserWallets();
  $rates = Money\Currency\ExchangeRate::getList();
  foreach ($wallets as $wallet) {
      ?>
      <div class="col-sm-4">
        <h4><?= $wallet->currency->name(); ?></h4>
        <b><?= number_format($wallet->amount, 4, '.', ' '); ?></b> <?= $wallet->currency->acronym(); ?><br />
        <?php
        $blocked = \Money\Wallet\Block::getList(['where' => ['wallet_id', $wallet->id]]);
        $blockedSum = 0;
        foreach ($blocked as $block) {
            $blockedSum += $block->amount;
        }
        if ($blockedSum) {
            echo "Заблокировано: {$blockedSum}<br />";
        }
        if ($wallet->currency->refill) {
            echo "<a href = '/money/refill?currency_id={$wallet->currency_id}'>Пополнить</a> ";
        }
        foreach ($rates as $rate) {
            if ($rate->currency_id == $wallet->currency_id && !empty($wallets[$rate->target_currency_id])) {
                echo "<a href = '/money/exchange?currency_id={$wallet->currency_id}'>Обменять</a>";
                break;
            }
            if ($rate->target_currency_id == $wallet->currency_id && !empty($wallets[$rate->currency_id])) {
                echo "<a href = '/money/exchange?target_currency_id={$wallet->currency_id}'>Обменять</a>";
                break;
            }
        }
        echo " <a href ='/money/transfer?currency_id={$wallet->currency_id}'>Перевести</a><br />";
        echo " <a href ='/users/cabinet/walletHistory?currency_id={$wallet->currency_id}'>История</a>";
        echo " <a href ='/users/cabinet/walletBlocked?currency_id={$wallet->currency_id}'>Блокировки</a>";
        ?>
      </div>
      <?php
  }
  ?>
</div>