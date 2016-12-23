<?php

/**
 * Noty library
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Libs;

class Noty extends \Object {

    public static $name = 'Noty';
    public static $bowerPacks = [
        'noty' => '2.3.*'
    ];
    public static $files = [
        'bower' => [
            'js' => [
                'noty/js/noty/packaged/jquery.noty.packaged.min.js'
            ]
        ]
    ];

}
