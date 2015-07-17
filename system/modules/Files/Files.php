<?php

class Files extends Module {

    /**
     * Загрузка файлов
     * 
     * $file - масив из переменной $_FILES[{input name}]
     * $options - массив из опций заливки 
     * --	[file_code]: уникальный код для системы медиаданых
     * --	[allow_types]: досупные для заливки типы файлов. Например image (тип форматов из таблицы типов файлов file_type_ext)
     */
    function upload($file, $options = array()) {

        $site_path = App::$primary->path;

        if (!is_uploaded_file($file['tmp_name']))
            return 0;

        $fileinfo = pathinfo($file['name']);
        if (empty($fileinfo['extension']))
            return 0;

        $type = Files\Type::get($fileinfo['extension'], 'ext');
        if (!$type)
            return 0;

        if (!empty($options['accept_group']) && $options['accept_group'] != $type->group) {
            return 0;
        }

        $fileObject = new Files\File();
        if (!empty($options['file_code'])) {
            $fileObject = Files\File::get($options['file_code'], 'code');
            if (!$fileObject) {
                $fileObject = new Files\File();
                $fileObject->code = $options['file_code'];
                $fileObject->name = microtime(true);
            }
            elseif(!$fileObject->name){
                $fileObject->name = microtime(true);
            }
        } else {
            $fileObject->name = microtime(true);
        }

        $fileObject->path = $type->type_dir . date('Y-m-d') . '/' . $fileObject->name . '.' . $fileinfo['extension'];
        if ($fileObject->id && file_exists($site_path . $fileObject->path))
            unlink($site_path . $fileObject->path);

        Tools::createDir($site_path . $type->type_dir . date('Y-m-d') . '/');

        if (!move_uploaded_file($file['tmp_name'], $site_path . $fileObject->path))
            return false;

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
     * --	[file_code]: уникальный код для системы медиаданых
     * --	[allow_types]: досупные для заливки типы файлов. Например image (тип форматов из таблицы типов файлов file_type_ext)
     */
    function uploadFromUrl($url, $options = array()) {
        $fileinfo = pathinfo($url);
        /*
          'dirname' => string '/userfiles' (length=10)
          'basename' => string 'avtokran(1).gif' (length=15)
          'extension' => string 'gif' (length=3)
          'filename' => string 'avtokran(1)' (length=11)
         */

        $site_path = App::$primary->path;
        Tools::createDir($site_path . '/static/tmp/');
        $file = @file_get_contents($url);
        if ($file === false) {
            return 0;
        }
        file_put_contents($site_path . '/static/tmp/' . $fileinfo['basename'], $file);
        /* if (!copy($url, App::$cur->app['path'] . '/static/tmp/' . $fileinfo['basename']))
          return false; */

        if (empty($fileinfo['extension']))
            return 0;

        $type = Files\Type::get($fileinfo['extension'], 'ext');
        if (!$type)
            return 0;

        $fileObject = new Files\File();
        if (!empty($options['file_code'])) {
            $fileObject = Files\File::get($options['file_code'], 'file_code');
        } else {
            $fileObject->name = microtime(true);
        }

        $fileObject->path = $type->type_dir . date('Y-m-d') . '/' . $fileObject->name . '.' . $fileinfo['extension'];
        if (!empty($options['file_code']) && file_exists($fileObject->path))
            unlink($path);

        Tools::createDir($site_path . $type->type_dir . date('Y-m-d') . '/');

        if (!copy($site_path . '/static/tmp/' . $fileinfo['basename'], $site_path . $fileObject->path))
            return false;
        unlink($site_path . '/static/tmp/' . $fileinfo['basename']);

        $fileObject->type_id = $type->pk();
        $fileObject->original_name = $fileinfo['basename'];
        $fileObject->date_create = 'CURRENT_TIMESTAMP';
        $fileObject->save();

        return $fileObject->id;
    }

}

?>
