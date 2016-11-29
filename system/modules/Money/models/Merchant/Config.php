<?php

/**
 * Merchant config
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Money\Merchant;

class Config extends \Model {

    public static $objectName = 'Опция системы оплаты';
    public static $cols = [
        'name' => ['type' => 'text'],
        'value' => ['type' => 'textarea'],
        'merchant_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'merchant'],
        'date_create' => ['type' => 'dateTime']
    ];
    public static $labels = [
        'name' => 'Название',
        'value' => 'Значение',
    ];
    public static $dataManagers = [
        'manager' => [
            'name' => 'Опции системы оплаты',
            'cols' => [
                'name',
                'value',
            ],
        ],
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['name'],
                ['value'],
            ]
    ]];

    public static function relations() {
        return [
            'merchant' => [
                'model' => 'Money\Merchant',
                'col' => 'merchant_id'
            ]
        ];
    }

}
