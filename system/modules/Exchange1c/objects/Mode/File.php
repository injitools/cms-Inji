<?php

/**
 * Mode File
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Exchange1c\Mode;

class File extends \Exchange1c\Mode
{
    public function process()
    {
        $dir = $this->exchange->path;
        \Tools::createDir($dir);
        $file = new \Exchange1c\Exchange\File();
        $file->name = $_GET['filename'];
        $file->exchange_id = $this->exchange->id;
        $file->size = ceil(filesize($dir . '/' . $_GET['filename']));
        $file->save();

        if (strpos($_GET['filename'], '/') !== false) {
            $subDir = substr($_GET['filename'], 0, strrpos($_GET['filename'], "/") + 1);
            \Tools::createDir($dir . '/' . $subDir);
        }
        $status = 'success';
        $text = '';
        if (false === file_put_contents($dir . '/' . $_GET['filename'], file_get_contents("php://input"))) {
            $status = 'failure';
            $text = 'Fail on save file: ' . $_GET['filename'];
        }
        if (strpos($_GET['filename'], '1cbitrix') !== false) {
            $data = new \SimpleXMLElement(file_get_contents($dir . '/' . $_GET['filename']));
            $orders = new \Exchange1c\Parser\Orders($data);
            $orders->process();
        }
        \App::$cur->exchange1c->response($status, $text, false);
        $this->end($status);
    }

}
