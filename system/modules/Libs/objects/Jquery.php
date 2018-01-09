<?php

/**
 * Jquery library
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\Libs;

class Jquery extends \Inji\InjiObject {

    public static $name = 'jQuery';
    public static $bowerPacks = [
        'jquery' => '2.1.*'
    ];
    public static $files = [
        'bower' => [
            'js' => [
                'jquery/dist/jquery.min.js'
            ]
        ]
    ];

}