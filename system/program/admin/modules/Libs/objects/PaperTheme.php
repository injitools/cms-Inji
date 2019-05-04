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

class PaperTheme {

    public static $name = 'PaperTheme';
    public static $bowerPacks = [
        'bootswatch-dist' => '3.3.6'
    ];
    public static $files = [
        'bower' => [
            'js' => [
                'bootswatch-dist/js/bootstrap.min.js'
            ],
            'css' => [
                'bootswatch-dist/css/bootstrap.min.css'
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
    public static $requiredLibs = [
        'jquery'
    ];

}