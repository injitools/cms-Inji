<h3>История счета</h3>
<?php
$currency_id = !empty($_GET['currency_id']) ? (int)$_GET['currency_id'] : 0;
$wallets = App::$cur->money->getUserWallets();
if ($currency_id && empty($wallets[$currency_id])) {
    Msg::add('У вас нет такого кошелька');
    Msg::show();
    return;
}

if ($currency_id) {
    $ids = $wallets[$currency_id]->id;
} else {
    $ids = [];
    foreach ($wallets as $wallet) {
        $ids[] = $wallet->id;
    }
    $ids = implode(',', $ids);
}
$table = new \Ui\Table();
$table->setCols([
    '№', 'Кошелек', 'Сумма', 'Комментарий', 'Дата'
]);

//items pages
$pages = new \Ui\Pages($_GET, [
    'count' => \Money\Wallet\History::getCount(['where' => ['wallet_id', $ids, 'IN']]),
    'limit' => 20,
]);
$histories = \Money\Wallet\History::getList([
    'where' => ['wallet_id', $ids, 'IN'],
    'order' => [['date_create', 'DESC'], ['id', 'DESC']],
    'start' => $pages->params['start'],
    'limit' => $pages->params['limit'],
]);
foreach ($histories as $history) {
    $amount = $history->amount ? $history->amount : $history->new - $history->old;
    $table->addRow([
        $history->id,
        $history->wallet->currency->name(),
        '<span class = "' . ($amount > 0 ? "text-success" : 'text-danger') . '">' . $amount . '</span>',
        $history->comment,
        $history->date_create
    ]);
}
$table->draw();
$pages->draw();