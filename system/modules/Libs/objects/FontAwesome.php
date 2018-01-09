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

class FontAwesome extends \Inji\InjiObject {

    public static $name = 'Font Awesome';
    public static $bowerPacks = [
        'components-font-awesome' => '*'
    ];
    public static $files = [
        'bower' => [
            'css' => [
                'components-font-awesome/css/font-awesome.min.css',
            ],
        ]
    ];

}