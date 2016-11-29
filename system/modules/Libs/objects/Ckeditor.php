<?php

/**
 * Ckeditor library
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Libs;

class Ckeditor extends \Object {

    public static $name = 'CKEditor';
    public static $composerPacks = [
        'ckeditor/ckeditor' => '4.*'
    ];
    public static $files = [
        'js' => [
            '/static/moduleAsset/libs/libs/ckeditor/path.js',
            'ckeditor/ckeditor/ckeditor.js',
            '/static/moduleAsset/libs/libs/ckeditor/bootstrap-ckeditor-fix.js',
            '/static/moduleAsset/libs/libs/ckeditor/jquery.adapter.js'
        ]
    ];
    public static $staticDirs = [
        'ckeditor/ckeditor'
    ];
    public static $programDirs = [
        'program'
    ];

}
