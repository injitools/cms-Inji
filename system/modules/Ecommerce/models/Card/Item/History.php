<?php

/**
 * Card item history
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ecommerce\Card\Item;

class History extends \Model {

    public static $cols = [
        //Основные параметры
        'card_item_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'cardItem'],
        'amount' => ['type' => 'decimal'],
        //Системные параметры
        'date_create' => ['type' => 'dateTime'],
    ];

    public static function relations() {
        return [
            'cardItem' => [
                'model' => 'Ecommerce\Card\Item',
                'col' => 'card_item_id'
            ]
        ];
    }

}
