<?php

/**
 * Statics
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class Statics {

    /**
     * Cached static file and return absolute url for client side use
     * 
     * @param string $path
     * @param string $resize
     * @param string $resizeCrop
     * @param string $resizePos
     * @return string
     */
    public static function file($path, $resize = '', $resizeCrop = '', $resizePos = '') {
        $absolutePath = App::$cur->staticLoader->parsePath($path);
        $convet = false;
        if (!file_exists($absolutePath) && file_exists(mb_convert_encoding($absolutePath, 'Windows-1251', 'UTF-8'))) {
            $absolutePath = mb_convert_encoding($absolutePath, 'Windows-1251', 'UTF-8');
            $convet = true;
        }
        if (!file_exists($absolutePath)) {
            return '';
        } else {
            $options = [];
            if ($resize) {
                $resize = explode('x', $resize);
                $options = ['resize' => ['x' => $resize[0], 'y' => $resize[1]]];
            }
            $options['crop'] = $resizeCrop;
            $options['pos'] = $resizePos;
            $path = Cache::file($absolutePath, $options);
            $path = $convet ? mb_convert_encoding($path, 'UTF-8', 'Windows-1251') : $path;
            return '/' . $path;
        }
    }

}
