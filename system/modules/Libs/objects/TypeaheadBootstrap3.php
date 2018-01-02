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

class TypeaheadBootstrap3 extends \InjiObject {

    public static $name = 'Typeahead Bootstrap 3';
    public static $bowerPacks = [
        'https://github.com/bassjobsen/Bootstrap-3-Typeahead' => '*'
    ];
    public static $files = [
        'bower' => [
            'js' => [
                'Bootstrap-3-Typeahead/bootstrap3-typeahead.min.js',
            ],
        ]
    ];

}