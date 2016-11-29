<?php

/**
 * Currency exchange rate history
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Money\Currency\ExchangeRate;

class History extends \Model {

    public static $cols = [
        'currency_exchangerate_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'rate'],
        'old' => ['type' => 'decimal'],
        'new' => ['type' => 'decimal'],
        'date_create' => ['type' => 'dateTime']
    ];

    public static function relations() {
        return [
            'rate' => [
                'model' => 'Money\Currency\ExchangeRate\History',
                'col' => 'currency_exchangerate_id'
            ],
        ];
    }

}
