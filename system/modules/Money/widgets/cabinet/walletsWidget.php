<big>Кошельки</big><br/>
<?php
$blocked = App::$cur->money->getUserBlocks();
$wallets = App::$cur->money->getUserWallets();
foreach ($wallets as $wallet) {
    ?>
    <b><?= $wallet->showAmount(); ?></b> <?= $wallet->currency->acronym(); ?><br/>
    <?php
}
?>
<a href='/money/deposit'>Пополнить</a>
<a href='/money/exchange'>Обменять</a>
<a href='/money/transfer'>Перевести</a>
