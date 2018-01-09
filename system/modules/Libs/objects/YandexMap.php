<?php

/**
 * YandexMap library
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\Libs;

class YandexMap extends \Inji\InjiObject {

    public static $name = 'YandexMap';
    public static $files = [
        'js' => [
            'http://api-maps.yandex.ru/2.1/?lang=ru_RU'
        ]
    ];

}
