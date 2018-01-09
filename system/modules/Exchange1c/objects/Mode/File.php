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

class File extends \Exchange1c\Mode {

    public function process() {
        $dir = $this->exchange->path;
        \Inji\Tools::createDir($dir);
        $file = new \Exchange1c\Exchange\File();
        $file->name = $_GET['filename'];
        $file->exchange_id = $this->exchange->id;
        $file->status = 'pending';
        $file->save();

        $filename = \Inji\Tools::parsePath($_GET['filename']);
        if (strpos($filename, '/') !== false) {
            $subDir = substr($filename, 0, strrpos($filename, "/") + 1);
            \Inji\Tools::createDir($dir . '/' . $subDir);
        }
        $text = '';
        if (false === file_put_contents($dir . '/' . $filename, file_get_contents("php://input"), FILE_APPEND)) {
            $text = 'Fail on save file: ' . $filename;
            $file->status = 'failure';
        } else {
            $file->size = ceil(filesize($dir . '/' . $filename));
            $file->name = $filename;
            $file->status = 'success';
        }
        if (strpos($filename, '1cbitrix') !== false) {
            $data = new \SimpleXMLElement(file_get_contents($dir . '/' . $filename));
            $orders = new \Exchange1c\Parser\Orders($data);
            $orders->process();
        }
        if ($file->status === 'success') {
            $pathinfo = pathinfo($filename);
            if ($pathinfo['extension'] === 'zip') {
                $zip = new \ZipArchive;
                if ($zip->open($dir . '/' . $filename) === TRUE) {
                    $zip->extractTo($dir);
                    $zip->close();
                } else {
                    //comment for chained files working
                    //$text = 'Fail on unzip file: ' . $filename;
                    //$file->status = 'failure';
                }
            }
        }
        $file->save();
        \App::$cur->exchange1c->response($file->status, $text, false);
        $this->end($file->status);
    }

}
