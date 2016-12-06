<?php

/**
 * Malihu Scrollbar library
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Libs;

class MalihuScrollbar extends \Object {

    public static $name = 'Malihu Scrollbar';
    public static $bowerPacks = [
        'malihu-custom-scrollbar-plugin' => '*'
    ];
    public static $files = [
        'bower' => [
            'css' => [
                'malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.min.css'
            ],
            'js' => [
                'malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js'
            ],
        ]
    ];

}