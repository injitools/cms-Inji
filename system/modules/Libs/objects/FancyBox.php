<?php

/**
 * Fancy Box library
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Libs;

class FancyBox extends \Object {

    public static $name = 'Fancy Box';
    public static $files = [
        'js' => [
            '/static/moduleAsset/libs/libs/fancybox/source/jquery.fancybox.pack.js',
        ],
        'css' => [
            '/static/moduleAsset/libs/libs/fancybox/source/jquery.fancybox.css'
        ]
    ];
    public static $requiredLibs = [
        'jquery'
    ];

}
