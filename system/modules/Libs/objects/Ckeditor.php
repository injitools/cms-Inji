<?php

/**
 * Ckeditor library
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\Libs;

class Ckeditor extends \Inji\InjiObject {

    public static $name = 'CKEditor';
    public static $bowerPacks = [
        'ckeditor' => '4.*'
    ];
    public static $files = [
        'bower' => [
            'js' => [
            ]
        ],
        'js' => [
            '/static/moduleAsset/libs/libs/ckeditor/path.js',
            '/cache/static/bowerLibs/ckeditor/ckeditor.js',
            '/static/moduleAsset/libs/libs/ckeditor/bootstrap-ckeditor-fix.js',
            '/static/moduleAsset/libs/libs/ckeditor/jquery.adapter.js'
        ]
    ];
    public static $programDirs = [
        'program'
    ];

}