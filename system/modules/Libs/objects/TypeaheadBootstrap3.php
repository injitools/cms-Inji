<?php

/**
 * Typeahead Bootstrap 3 library
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Libs;

class TypeaheadBootstrap3 extends \Object {

    public static $name = 'Typeahead Bootstrap 3';
    public static $composerPacks = [
        'bassjobsen/bootstrap-3-typeahead' => '4.*'
    ];
    public static $files = [
        'js' => [
            'bassjobsen/bootstrap-3-typeahead/bootstrap3-typeahead.min.js'
        ]
    ];
    public static $staticDirs = [
        'bassjobsen/bootstrap-3-typeahead'
    ];

}
