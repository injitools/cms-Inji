<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ecommerce\Item;

/**
 * Description of Review
 *
 * @author benzu
 */
class Review extends \Model {

    public static $cols = [
        'item_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'item'],
        'user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'user'],
        'name' => ['type' => 'text'],
        'mail' => ['type' => 'email'],
        'text' => ['type' => 'textarea'],
        'rating' => ['type' => 'number'],
        'file_id' => ['type' => 'file'],
        'status' => ['type' => 'select', 'source' => 'array', 'default' => 'new', 'sourceArray' => [
            'new' => 'Новый',
            'accept' => 'Принят',
            'denied' => 'Недопущен'
        ]
        ],
        'voteup' => ['type' => 'number'],
        'votedown' => ['type' => 'number'],
        'date_create' => ['type' => 'dateTime']
    ];
    public static $labels = [
        'item_id' => 'Товар',
        'user_id' => 'Пользователь',
        'name' => 'Имя',
        'mail' => 'Email',
        'text' => 'Текст отзыва',
        'rating' => 'Оценка',
        'file_id' => 'Вложение',
        'status' => 'Статус модерации',
        'date_create' => 'Дата'
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => ['name', 'item_id', 'status', 'mail', 'user_id', 'rating', 'date_create']
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['name', 'mail'],
                ['text'],
                ['rating', 'status'],
                ['voteup', 'votedown']
            ]
        ]
    ];

    public static function relations() {
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