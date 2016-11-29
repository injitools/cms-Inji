<?php

/**
 * Jquery library
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Libs;

class Jquery extends \Object {

    public static $name = 'jQuery';
    public static $composerPacks = [
        'components/jquery' => '2.1.*'
    ];
    public static $files = [
        'js' => [
            'components/jquery/jquery.min.js'
        ]
    ];
    public static $staticDirs = [
        'components/jquery'
    ];

}
