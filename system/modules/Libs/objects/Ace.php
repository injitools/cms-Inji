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

class Ace extends \Object {

    public static $name = 'Ace editor';
    public static $files = [
        'js' => [
            '/static/moduleAsset/libs/libs/ace/js/src-noconflict/ace.js',
        ],
    ];

}
