<?php

return function ($step = NULL, $params = []) {
    // стандартыне статусы
    $statuses = [
        [
            'name' => 'Оформляется',
            'type' => 'process',
            'code' => 'info',
        ],
        [
            'name' => 'Оформлен',
            'type' => 'complete',
            'code' => 'primary',
        ],
        [
            'name' => 'Готовится к откгрузке',
            'type' => 'accept',
            'code' => 'success',
        ],
        [
            'name' => 'Отменен',
            'type' => 'cancel',
            'code' => 'danger',
        ],
        [
            'name' => 'Закрыт',
            'type' => 'close',
            'code' => 'default',
        ],
        [
            'name' => 'Обработан',
            'type' => 'read',
            'code' => 'muted',
        ]
    ];
    foreach ($statuses as $status) {
        $statusObj = new \Inji\Ecommerce\Cart\Status($status);
        $statusObj->save();
    }
    //Стандартные типы событий
    $types = [
        [
            'name' => 'Добавление товара'
        ],
        [
            'name' => 'Удаление товара'
        ],
        [
            'name' => 'Изменение цены'
        ],
        [
            'name' => 'Изменение количества'
        ],
        [
            'name' => 'Изменение статуса'
        ],
    ];
    foreach ($types as $type) {
        $typeObj = new \Ecommerce\Cart\Event\Type($type);
        $typeObj->save();
    }

    Inji::$inst->listen('modelItemParamsChanged-Ecommerce\Cart', 'Ecommerce-cartStatusDetector', [
        'module' => 'Ecommerce',
        'method' => 'cartStatusDetector'
    ], true);

    Inji::$inst->listen('ecommerceCartClosed', 'Ecommerce-cardTrigger', [
        'module' => 'Ecommerce',
        'method' => 'cardTrigger'
    ], true);
    Inji::$inst->listen('ecommerceCartClosed', 'Ecommerce-bonusTrigger', [
        'module' => 'Ecommerce',
        'method' => 'bonusTrigger'
    ], true);
};
