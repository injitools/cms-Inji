<?php

/**
 * Exchange rate
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Money\Currency;

class ExchangeRate extends \Model
{
    static $objectName = 'Курс обмена';
    static $labels = [
        'currency_id' => 'Валюта',
        'target_currency_id' => 'Итоговая валюта',
        'rate' => 'Курс',
    ];
    static $cols = [
        'currency_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'currency'],
        'target_currency_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'targetCurrency'],
        'rate' => ['type' => 'text'],
    ];
    static $dataManagers = [
        'manager' => [
            'name' => 'Курсы обмена',
            'cols' => ['currency_id', 'target_currency_id', 'rate']
        ]
    ];
    static $forms = [
        'manager' => [
            'map' => [
                ['currency_id', 'target_currency_id'],
                ['rate'],
            ]
        ]
    ];

    static function relations()
    {
        return [
            'currency' => [
                'model' => 'Money\Currency',
                'col' => 'currency_id'
            ],
            'targetCurrency' => [
                'model' => 'Money\Currency',
                'col' => 'target_currency_id'
            ]
        ];
    }

    function beforeSave()
    {
        if ($this->pk()) {
            $cur = ExchangeRate::get($this->pk());
            if ($cur->rate != $this->rate) {
                $history = new ExchangeRate\History();
                $history->currency_exchangerate_id = $this->pk();
                $history->old = $cur->rate;
                $history->new = $this->rate;
                $history->save();
            }
        }
    }

}
