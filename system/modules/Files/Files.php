<?php

/**
 * Files module
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class Files extends Module {

    /**
     * Загрузка файлов
     *
     * $file - масив из переменной $_FILES[{input name}]
     * $options - массив из опций заливки
     * --    [file_code]: уникальный код для системы медиаданых
     * --    [allow_types]: досупные для заливки типы файлов. Например image (тип форматов из таблицы типов файлов file_type_ext)
     */
    public function upload($file, $options = []) {

        $sitePath = App::$primary->path;

        if (!is_uploaded_file($file['tmp_name'])) {
            return 0;
        }

        $fileinfo = pathinfo($file['name']);
        if (empty($fileinfo['extension'])) {
            return 0;
        }

        $type = Files\Type::get($fileinfo['extension'], 'ext');
        if (!$type) {
            return 0;
        }

        if (!empty($options['accept_group']) && $options['accept_group'] != $type->group) {
            return 0;
        }

        $fileObject = new Files\File();
        if (!empty($options['file_code'])) {
            $fileObject = Files\File::get($options['file_code'], 'code');
            if (!$fileObject) {
                $fileObject = new Files\File();
                $fileObject->code = $options['file_code'];
            }
        }
        $fileObject->name = $fileinfo['filename'];
        $fileObject->path = $type->type_dir . date('Y-m-d') . '/' . microtime(true) . '.' . $fileinfo['extension'];
        if ($fileObject->id && file_exists($sitePath . $fileObject->path)) {
            unlink($sitePath . $fileObject->path);
        }

        Tools::createDir($sitePath . $type->type_dir . date('Y-m-d') . '/');

        if (!move_uploaded_file($file['tmp_name'], $sitePath . $fileObject->path)) {
            return false;
        }

        if ($type->allow_resize && $type->options && json_decode($type->options, true)) {
            $typeOptions = json_decode($type->options, true);
            list($img_width, $img_height) = getimagesize($sitePath . $fileObject->path);
            if ($img_height > $typeOptions['max_height'] || $img_width > $typeOptions['max_width']) {
                Tools::resizeImage($sitePath . $fileObject->path, $typeOptions['max_width'], $typeOptions['max_height']);
            }
        }

        $fileObject->type_id = $type->pk();
        $fileObject->original_name = $file['name'];
        $fileObject->upload_code = !empty($options['upload_code']) ? $options['upload_code'] : 'untracked';
        $fileObject->save();

        return $fileObject->id;
    }

    /**
     * Загрузка файлов по урл
     *
     * $url - адрес файла
     * $options - массив из опций заливки
     * --    [file_code]: уникальный код для системы медиаданых
     * --    [allow_types]: досупные для заливки типы файлов. Например image (тип форматов из таблицы типов файлов file_type_ext)
     */
    public function uploadFromUrl($url, $options = []) {
        $sitePath = App::$primary->path;
        if (empty($options['fileinfo'])) {
            $fileinfo = pathinfo($url);
        } else {
            $fileinfo = $options['fileinfo'];
        }

        if (empty($fileinfo['extension'])) {
            return 0;
        }
        $ext = $fileinfo['extension'];


        $type = Files\Type::get($ext, 'ext');
        if (!$type) {
            return 0;
        }

        if (!empty($options['accept_group']) && $options['accept_group'] != $type->group) {
            return 0;
        }

        $fileObject = new Files\File();
        if (!empty($options['file_code'])) {
            $fileObject = Files\File::get($options['file_code'], 'code');
            if (!$fileObject) {
                $fileObject = new Files\File();
                $fileObject->code = $options['file_code'];
            }
        }
        $fileObject->name = $fileinfo['filename'];
        $fileObject->path = $type->type_dir . date('Y-m-d') . '/' . microtime(true) . '.' . $ext;
        if ($fileObject->id && file_exists($sitePath . $fileObject->path)) {
            unlink($sitePath . $fileObject->path);
        }

        Tools::createDir($sitePath . $type->type_dir . date('Y-m-d') . '/');

        $file = @file_get_contents($url);
        if ($file === false) {
            return 0;
        }
        if (!file_put_contents($sitePath . $fileObject->path, $file)) {
            return 0;
        }

        if ($type->allow_resize && $type->options && json_decode($type->options, true)) {
            $typeOptions = json_decode($type->options, true);
            list($img_width, $img_height) = getimagesize($sitePath . $fileObject->path);
            if ($img_height > $typeOptions['max_height'] || $img_width > $typeOptions['max_width']) {
                Tools::resizeImage($sitePath . $fileObject->path, $typeOptions['max_width'], $typeOptions['max_height']);
            }
        }

        $fileObject->type_id = $type->pk();
        $fileObject->original_name = $fileinfo['basename'];
        $fileObject->upload_code = !empty($options['upload_code']) ? $options['upload_code'] : 'untracked';
        $fileObject->save();

        return $fileObject->id;
    }
}