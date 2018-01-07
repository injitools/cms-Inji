<?php
return [
    'autoloadModules' => [],
    'modules' => [
        'Dashboard',
        'StaticLoader',
        'Ui',
        'Db',
        'View',
        'Libs',
        'Server',
        'Modules',
        'Apps',
        'Menu',
        'Events',
        'I18n',
        'Daemon',
    ],
    'moduleRouter' => [
        'static' => 'StaticLoader'
    ],
    'assets' => [
        'js' => [
            [
                'file' => '/static/moduleAsset/Server/js/Server.js',
                'name' => 'Server',
                'libs' => ['noty']
            ]
        ]
    ]
];