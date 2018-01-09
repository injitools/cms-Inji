<?php
/**
 * App
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\Apps;

class App extends \Inji\Model {

    /**
     * Model options
     */
    public static $objectName = 'App options';
    public static $labels = [
        'id' => '#',
        'name' => 'Название',
        'dir' => 'Директория',
        'installed' => 'Установлен',
        'default' => 'По умолчанию',
        'route' => 'Роут',
    ];
    public static $cols = [
        'id' => ['type' => 'pk'],
        'name' => ['type' => 'text'],
        'dir' => ['type' => 'text'],
        'installed' => ['type' => 'bool'],
        'default' => ['type' => 'bool'],
        'route' => ['type' => 'text'],
    ];
    public static $dataManagers = [
        'setup' => [
            'name' => 'Настроенные приложения',
            'options' => [
                'access' => [
                    'apps' => [
                        'setup'
                    ]
                ]
            ],
            'cols' => [
                'name',
                'dir',
                'installed',
                'default',
                'route',
            ],
            'activeForm' => 'setup',
            'actions' => [
                'manage' => [
                    'className' => 'Href',
                    'href' => '/setup/apps/configure',
                    'text' => '<i class = "glyphicon glyphicon-cog"></i>'
                ],
                'Edit', 'Delete'
            ],
        ]
    ];
    public static $forms = [
        'setup' => [
            'name' => 'Приложение',
            'options' => [
                'access' => [
                    'apps' => [
                        'setup'
                    ]
                ]
            ],
            'map' => [
                ['name', 'dir'],
                ['installed', 'default'],
                ['route'],
            ]
        ]
    ];

    public static function index() {
        return 'id';
    }

}
