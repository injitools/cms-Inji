<?php

return [
    'name' => 'Онлайн оплата',
    'handler' => function ($cart) {
        if (\App::$cur->money) {
            $sums = $cart->finalSum();
            foreach ($sums->sums as $currency_id => $sum) {
                if (!$currency_id && empty(\App::$cur->money->config['defaultCurrency'])) {
                    continue;
                } elseif (!$currency_id) {
                    $currency_id = \App::$cur->money->config['defaultCurrency'];
                }
                $pay = \Money\Pay::get([
                    ['data', $cart->id],
                    ['currency_id', $currency_id],
                    ['user_id', \Users\User::$cur->id]
                ]);
                if (!$pay) {
                    $pay = new Money\Pay([
                        'data' => $cart->id,
                        'currency_id' => $currency_id,
                        'user_id' => \Users\User::$cur->id,
                        'sum' => $sum,
                        'description' => 'Оплата заказа №' . $cart->id . ' в онлайн-магазине',
                        'type' => 'pay',
                        'pay_status_id' => 1,
                        'callback_module' => 'Ecommerce',
                        'callback_method' => 'cartPayRecive'
                    ]);
                    $pay->save();
                } elseif ($pay->sum != $sum) {
                    $pay->sum = $sum;
                    $pay->save();
                }
            }
            return ['/money/merchants/pay/?data=' . $cart->id, 'Ваш заказ был создан. Вам необходимо оплатить счета, после чего с вами свяжется администратор для уточнения дополнительной информации', 'success'];
        }
    }
];
        