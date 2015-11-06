<?php

/**
 * Item name
 *
 * Info
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Db;

class Options extends \Model
{
    /**
     * Model options
     */
    static $objectName = 'Db options';
    static $storage = ['type' => 'moduleConfig', 'options' => ['share' => true]];
    static $labels = [
        'id' => '#',
        'connect_name' => 'Название',
        'connect_alias' => 'Алиас',
        'driver' => 'Тип базы',
        'host' => 'Хост',
        'user' => 'Пользователь',
        'pass' => 'Пароль',
        'db_name' => 'Название базы',
        'encoding' => 'Кодировка',
        'table_prefix' => 'Префикс',
        'port' => 'порт',
    ];
    static $cols = [
        'id' => ['type' => 'pk'],
        'connect_name' => ['type' => 'text', 'default' => 'local'],
        'connect_alias' => ['type' => 'text', 'default' => 'local'],
        'driver' => ['type' => 'select', 'source' => 'array', 'sourceArray' => ['Mysql' => 'Mysql']],
        'host' => ['type' => 'text', 'default' => 'localhost'],
        'user' => ['type' => 'text', 'default' => 'root'],
        'pass' => ['type' => 'text'],
        'db_name' => ['type' => 'text', 'default' => 'test'],
        'encoding' => ['type' => 'text', 'default' => 'utf8'],
        'table_prefix' => ['type' => 'text', 'default' => 'inji_'],
        'port' => ['type' => 'text', 'default' => '3306'],
    ];
    static $dataManagers = [
        'setup' => [
            'name' => 'Настройки соединения с БД',
            'options' => [
                'access' => [
                    'apps' => [
                        'setup'
                    ]
                ]
            ],
            'cols' => [
                'connect_name',
                'connect_alias',
                'db_name'
            ],
            'rowButtons' => [
                'edit', 'delete'
            ]
        ]
    ];
    static $forms = [
        'manager' => [
            'name' => 'Соединение с БД',
            'map' => [
                ['connect_name', 'connect_alias', 'driver'],
                ['host', 'user'],
                ['pass', 'db_name'],
                ['encoding', 'table_prefix', 'port']
            ]
        ]
    ];

    function name()
    {
        return $this->connect_name;
    }

    static function index()
    {
        return 'id';
    }

}
