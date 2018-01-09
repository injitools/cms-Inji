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

class Ace extends \Inji\InjiObject {

    public static $name = 'Ace editor';
    public static $bowerPacks = [
        'ace-builds' => '*'
    ];
    public static $files = [
        'bower' => [
            'js' => [
                'ace-builds/src-min-noconflict/ace.js',
            ],
        ]
    ];

}