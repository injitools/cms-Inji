<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ecommerce;

/**
 * Description of Favorite
 *
 * @author benzu
 */
class Favorite extends \Model {

    static $cols = [
        'user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'user'],
        'item_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'item'],
        'date_create'=>['type'=>'dateTime']
    ];

    static function relations() {
        return [
            'user' => [
                'col' => 'user_id',
                'model' => 'Users\User'
            ],
            'item' => [
                'col' => 'item_id',
                'model' => 'Ecommerce\Item'
            ],
        ];
    }
}