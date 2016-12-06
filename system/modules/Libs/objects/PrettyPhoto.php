<?php

/**
 * PrettyPhoto library
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Libs;

class PrettyPhoto extends \Object {

    public static $name = 'PrettyPhoto';
    public static $bowerPacks = [
        'https://github.com/scaron/prettyphoto' => '*'
    ];
    public static $files = [
        'bower' => [
            'js' => [
                'prettyphoto/js/jquery.prettyPhoto.js',
            ],
            'css' => [
                'prettyphoto/css/prettyPhoto.css'
            ]
        ]
    ];
    public static $requiredLibs = [
        'jquery'
    ];

}