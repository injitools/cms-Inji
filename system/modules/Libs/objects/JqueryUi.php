<?php

/**
 * JqueryUi library
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Libs;

class JqueryUi extends \Object {

    public static $name = 'jQuery Ui';
    public static $composerPacks = [
        'components/jqueryui' => '1.11.*'
    ];
    public static $bowerPacks = [
        'jqueryui-timepicker-addon' => '*',
        'https://github.com/jquery-ui-bootstrap/jquery-ui-bootstrap' => '*'
    ];
    public static $files = [
        'js' => [
            'components/jqueryui/jquery-ui.min.js',
            'components/jqueryui/ui/i18n/datepicker-ru.js',
        ],
        'bower' => [
            'js' => [
                'jqueryui-timepicker-addon/dist/jquery-ui-timepicker-addon.min.js',
                'jqueryui-timepicker-addon/dist/i18n/jquery-ui-timepicker-ru.js'
            ],
            'css' => [
                'jquery-ui-bootstrap/css/custom-theme/jquery-ui-1.10.3.custom.css',
                'jqueryui-timepicker-addon/dist/jquery-ui-timepicker-addon.min.css'
            ],
        ]
    ];
    public static $staticDirs = [
        'components/jqueryui'
    ];
    public static $requiredLibs = [
        'jquery'
    ];

}