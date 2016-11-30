<?php

/**
 * Widgets admin controller
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class WidgetsController extends Controller {

    public function widgetChooserAction() {
        $widgets = [];
        foreach (App::$primary->config['modules'] as $module) {
            $info = Module::getInfo($module);
            if (!empty($info['widgets'])) {
                $widgets += $info['widgets'];
            }
        }
        $this->view->page(['page' => 'blank', 'data' => compact('widgets')]);
    }

    public function widgetImageAction() {
        if (!empty($_GET['text'])) {
            $widgetCode = explode(':', preg_replace('!^{WIDGET:!isU', '', preg_replace('!}$!isU', '', urldecode($_GET['text']))));
            $text = 'Виджет: ';
            $widget = false; //Widget::get($widgetCode[0], 'widget_filename');

            if ($widget) {
                $text .= $widget->widget_name . "\n";
                $i = 1;
                if (isset($widgetCode[$i]) && $widget->widget_params) {
                    $params = json_decode($widget->widget_params, true);
                    if ($params) {
                        foreach ($params as $param) {
                            if (!isset($widgetCode[$i])) {
                                                            break;
                            }
                            if ($param['type'] == 'select') {
                                $item = $param['model']::get($widgetCode[$i++]);
                                if ($item) {
                                    $text .= $param['name'] . ': ' . $item->$param['showCol'] . "\n";
                                } else {
                                    $text .= $widgetCode[$i - 1];
                                }
                            } else {
                                $value = $widgetCode[$i++];
                                if (mb_strlen($value, 'utf-8') > 50) {
                                    $value = mb_substr($value, 0, 50) . '...';
                                }
                                $text .= $param['name'] . ': ' . $value . "\n";
                            }
                        }
                    }
                } else {
                    unset($widgetCode[0]);
                    foreach ($widgetCode as $item) {
                        $text .= $item . "\n";
                    }
                }
            } else {
                foreach ($widgetCode as $item) {
                    $text .= $item . "\n";
                }
            }
        } else {
            $text = 'text not defined';
        }

        header('Content-type: image/png');
        // шрифт
        $font = dirname(__FILE__) . '/../fonts/Cousine/Cousine-Regular.ttf';
        // вычисляем сколько места займёт текст
        $bbox = imageftbbox(10, 0, $font, $text);




        $width = abs($bbox[0]) + abs($bbox[2]); // distance from left to right
        $height = abs($bbox[1]) + abs($bbox[5]); // distance from top to bottom
        // размер изображения
        $img = imagecreatetruecolor($width + 10, $height + 10);
        // цвет текста
        $black = imagecolorallocate($img, 255, 255, 255);

        // цвет фона
        $bg = imagecolorallocate($img, 85, 101, 115);
        imagefilledrectangle($img, 0, 0, $width + 10, $height + 10, $bg);

        // вычисляем координаты для центрирования
        $x = (imagesx($img) - $bbox[4]) / 2;
        $y = (imagesy($img) - $bbox[5] - $bbox[3]) / 2;

        // добавляем текст на изображение
        imagefttext($img, 10, 0, $x, $y, $black, $font, $text);

        // выводим изображение
        imagepng($img);
        // освобождаем память
        imagedestroy($img);
    }

}
