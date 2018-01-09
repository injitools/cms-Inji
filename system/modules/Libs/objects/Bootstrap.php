<?php

/**
 * Bootstrap library
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\Libs;

class Bootstrap extends \Inji\InjiObject {

    public static $name = 'BootStrap';
    public static $bowerPacks = [
        'bootstrap' => '3'
    ];
    public static $files = [
        'bower' => [
            'js' => [
                'bootstrap/dist/js/bootstrap.min.js',
            ],
            'css' => [
                'bootstrap/dist/css/bootstrap.min.css',
            ]
        ],
        'js' => [
            '/static/moduleAsset/libs/libs/bootstrap/js/modalStack.js',
            '/static/moduleAsset/libs/libs/bootstrap/js/treeView.js'
        ],
        'css' => [
            '/static/moduleAsset/libs/libs/bootstrap/css/treeView.css'
        ]
    ];
    public static $staticDirs = [
        'twbs/bootstrap/dist'
    ];
    public static $requiredLibs = [
        'jquery'
    ];

}
