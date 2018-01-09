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

class ChartJs extends \Inji\InjiObject {

    public static $name = 'ChartJs';
    public static $composerPacks = [
        'nnnick/chartjs' => 'dev-master'
    ];
    public static $files = [
        'js' => [
            'nnnick/chartjs/Chart.min.js',
        ],
    ];
    public static $staticDirs = [
        'nnnick/chartjs'
    ];

}
