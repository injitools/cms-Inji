<?php

namespace Comments;

class Comment extends \Inji\Model {

    public static $objectName = 'Комментарии';
    static $cols = [
        'object_code' => ['type' => 'text'],
        'object_href' => ['type' => 'text'],
        'user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'user'],
        'text' => ['type' => 'textarea'],
        'vote_up' => ['type' => 'number'],
        'vote_down' => ['type' => 'number'],
        'moderate_status' => ['type' => 'select', 'source' => 'array',
            'sourceArray' => [
                'new' => 'Новый',
                'denied' => 'Отколнен модератором',
                'publish' => 'Опубликован'
            ],
            'default' => 'new'
        ],
        'date_publish' => ['type' => 'dateTime'],
        'date_create' => ['type' => 'dateTime'],
    ];
    static $dataManagers = [
        'manager' => [
            'cols' => ['object_code', 'user_id', 'text', 'vote_up', 'vote_down', 'moderate_status', 'date_publish', 'date_create']
        ]
    ];

}