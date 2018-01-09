<?php

/**
 * Item offer bonus
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\Ecommerce\Item\Offer;

class Bonus extends \Inji\Model {

    public static $cols = [
        //Основные параметры
        'item_offer_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'offer'],
        'type' => ['type' => 'text'],
        'value' => ['type' => 'text'],
        'count' => ['type' => 'decimal'],
        'limited' => ['type' => 'bool'],
        'left' => ['type' => 'number'],
        //Системные
        'date_create' => ['type' => 'dateTime']
    ];

    public static function relations() {
        return [
            'offer' => [
                'model' => 'Inji\Ecommerce\Item\Offer',
                'col' => 'item_offer_id'
            ]
        ];
    }

}
