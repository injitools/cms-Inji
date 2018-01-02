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

class JqueryUi extends \InjiObject {

    public static $name = 'jQuery Ui';
    public static $bowerPacks = [
        'jquery-ui' => '1.11.*',
        'jqueryui-timepicker-addon' => '*',
        'https://github.com/jquery-ui-bootstrap/jquery-ui-bootstrap' => '*'
    ];
    public static $files = [
        'bower' => [
            'js' => [
                'jquery-ui/jquery-ui.min.js',
                'jquery-ui/ui/i18n/datepicker-ru.js',
                'jqueryui-timepicker-addon/dist/jquery-ui-timepicker-addon.min.js',
                'jqueryui-timepicker-addon/dist/i18n/jquery-ui-timepicker-ru.js'
            ],
            'css' => [
                'jquery-ui-bootstrap/css/custom-theme/jquery-ui-1.10.3.custom.css',
                'jqueryui-timepicker-addon/dist/jquery-ui-timepicker-addon.min.css'
            ],
        ]
    ];
    public static $requiredLibs = [
        'jquery'
    ];

}