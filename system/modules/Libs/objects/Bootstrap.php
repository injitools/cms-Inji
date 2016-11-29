<?php

/**
 * Bootstrap library
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Libs;

class Bootstrap extends \Object {

    public static $name = 'BootStrap';
    public static $composerPacks = [
        'twbs/bootstrap' => '3.*'
    ];
    public static $files = [
        'js' => [
            'twbs/bootstrap/dist/js/bootstrap.min.js',
            '/static/moduleAsset/libs/libs/bootstrap/js/modalStack.js',
            '/static/moduleAsset/libs/libs/bootstrap/js/treeView.js'
        ],
        'css' => [
            'twbs/bootstrap/dist/css/bootstrap.min.css',
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
