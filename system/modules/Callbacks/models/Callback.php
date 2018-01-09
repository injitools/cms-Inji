<?php

/**
 * Callback
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Callbacks;

class Callback extends \Inji\Model {

    public static $objectName = "Отзыв";
    public static $categoryModel = 'Callbacks\Category';
    public static $cols = [
        'name' => ['type' => 'text'],
        'profession' => ['type' => 'text'],
        'original_url' => ['type' => 'text'],
        'view' => ['type' => 'bool'],
        'phone' => ['type' => 'text'],
        'mail' => ['type' => 'text'],
        'youtube_url' => ['type' => 'text'],
        'image_file_id' => ['type' => 'image'],
        'category_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'category'],
        'callback_type_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'type'],
        'user_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'user'],
        'text' => ['type' => 'html'],
        'weight' => ['type' => 'number'],
        'date_create' => ['type' => 'dateTime']
    ];
    public static $labels = [
        'name' => 'Имя',
        'category_id' => 'Категория',
        'original_url' => 'Ссылка на оригинал',
        'profession' => 'Профессия',
        'youtube_url' => 'Ссылка видео на YouTube',
        'view' => 'Отображается',
        'image_file_id' => 'Фото',
        'mail' => 'E-mail',
        'callback_type_id' => 'Тип отзыва',
        'text' => 'Текст',
        'user_id' => 'Пользователь',
        'phone' => 'Телефон',
        'date_create' => 'Дата создания'
    ];

    public static function relations() {
        return [
            'user' => [
                'model' => 'Users\User',
                'col' => 'user_id'
            ],
            'type' => [
                'model' => 'Callbacks\Callback\Type',
                'col' => 'callback_type_id'
            ],
            'category' => [
                'model' => 'Callbacks\Category',
                'col' => 'category_id'
            ],
            'image' => [
                'model' => 'Files\File',
                'col' => 'image_file_id'
            ]
        ];
    }

    public static $dataManagers = [
        'manager' => [
            'name' => 'Отзывы',
            'cols' => [
                'name', 'category_id', 'user_id', 'view', 'youtube_url', 'date_create'
            ],
            'filters' => [
                'name', 'profession', 'phone', 'text', 'view', 'user_id', 'date_create'
            ],
            'categorys' => [
                'model' => 'Callbacks\Category',
            ],
            'sortMode' => true
        ]
    ];
    public static $forms = [
        'manager' => [
            'map' => [
                ['name', 'phone'],
                ['profession', 'image_file_id'],
                ['callback_type_id', 'view'],
                ['mail', 'user_id'],
                ['category_id', 'youtube_url'],
                ['original_url'],
                ['text'],
            ]
        ]
    ];

}
