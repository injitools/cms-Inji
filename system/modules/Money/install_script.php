<?php

return function($step = NULL, $params = []) {

    $currencies = [
        [
            'name' => 'Доллары',
            'code' => '$'
        ],
        [
            'name' => 'Евро',
            'code' => '€'
        ],
        [
            'name' => 'Рубли',
            'code' => 'Р'
        ],
    ];
    foreach ($currencies as $currency) {
        $currencyObject = new \Money\Currency($currency);
        $currencyObject->save();
    }
    $statuses = [
        [
            'name' => 'Создан',
            'code' => 'new'
        ],
        [
            'name' => 'Оплачен',
            'code' => 'success'
        ],
        [
            'name' => 'Отменен',
            'code' => 'cancel'
        ],
        [
            'name' => 'Ошибка',
            'code' => 'error'
        ]
    ];
    foreach ($statuses as $status) {
        $statusObj = new \Money\Pay\Status($status);
        $statusObj->save();
    }
    $merchants = [
        [
            'name' => 'Wallet One',
            'object_name' => 'WalletOne',
            'image_file_id' => App::$cur->Files->uploadFromUrl(__DIR__ . '/static/images/WalletOne.png')
        ],
        [
            'name' => 'Robokassa',
            'object_name' => 'Robokassa',
            'image_file_id' => App::$cur->Files->uploadFromUrl(__DIR__ . '/static/images/Robokassa.png')
        ],
        [
            'name' => 'Payeer',
            'object_name' => 'Payeer',
            'image_file_id' => App::$cur->Files->uploadFromUrl(__DIR__ . '/static/images/Payeer.png')
        ],
        [
            'name' => 'PerfectMoney',
            'object_name' => 'PerfectMoney',
            'image_file_id' => App::$cur->Files->uploadFromUrl(__DIR__ . '/static/images/PerfectMoney.png')
        ],
    ];
    $merchantsConfig = [
        [
            [
                'name' => 'shopId',
                'value' => ''
            ],
            [
                'name' => 'secret',
                'value' => ''
            ]
        ],
        [
            [
                'name' => 'login',
                'value' => ''
            ],
            [
                'name' => 'pass1',
                'value' => ''
            ],
            [
                'name' => 'pass2',
                'value' => ''
            ]
        ],
        [
            [
                'name' => 'shopId',
                'value' => ''
            ],
            [
                'name' => 'secret',
                'value' => ''
            ]
        ],
        [
            []
        ]
    ];
    $merchantsCurrencies = [
        [
            [
                'currency_id' => 3,
                'code' => '643'
            ],
        ],
        [
            [
                'currency_id' => 3,
                'code' => 'RUB'
            ],
        ],
        [
        ],
        [
            [
                'currency_id' => 1,
                'code' => 'USD'
            ],
        ]
    ];
    foreach ($merchants as $key => $merchant) {
        $merchantObj = new \Money\Merchant($merchant);
        $merchantObj->save();
        foreach ($merchantsConfig[$key] as $config) {
            $merchantConfigObj = new Money\Merchant\Config($config);
            $merchantConfigObj->merchant_id = $merchantObj->id;
            $merchantConfigObj->save();
        }
        foreach ($merchantsCurrencies[$key] as $currency) {
            $currencyObj = new Money\Merchant\Currency($currency);
            $currencyObj->merchant_id = $merchantObj->id;
            $currencyObj->save();
        }
    }
    Inji::$inst->listen('ecommerceCartClosed', 'Money-rewardTrigger', [
        'module' => 'Money',
        'method' => 'rewardTrigger'
            ], true);
};
