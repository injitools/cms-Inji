<?php

/**
 * Js-Cookie library
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Libs;

class JsCookie extends \InjiObject {

    public static $name = 'JavaScript Cookie';
    public static $bowerPacks = [
        'js-cookie' => '*'
    ];
    public static $files = [
        'bower' => [
            'js' => [
                'js-cookie/src/js.cookie.js',
            ],
        ]
    ];

}