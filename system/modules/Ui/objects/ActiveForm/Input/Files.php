<?php

/**
 * Active form input files
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\Ui\ActiveForm\Input;

class Files extends \Inji\Ui\ActiveForm\Input {

    public function parseRequest($request) {
        if (!empty($_FILES[$this->activeForm->requestFormName]['tmp_name'][$this->modelName][$this->colName])) {
            $file_ids = !empty($request[$this->colName]) ? $request[$this->colName] : [];
            foreach ($_FILES[$this->activeForm->requestFormName]['tmp_name'][$this->modelName][$this->colName] as $key => $tmp_name) {
                $file_ids[] = \App::$primary->files->upload([
                    'tmp_name' => $_FILES[$this->activeForm->requestFormName]['tmp_name'][$this->modelName][$this->colName][$key],
                    'name' => $_FILES[$this->activeForm->requestFormName]['name'][$this->modelName][$this->colName][$key],
                    'type' => $_FILES[$this->activeForm->requestFormName]['type'][$this->modelName][$this->colName][$key],
                    'size' => $_FILES[$this->activeForm->requestFormName]['size'][$this->modelName][$this->colName][$key],
                    'error' => $_FILES[$this->activeForm->requestFormName]['error'][$this->modelName][$this->colName][$key],
                        ], [
                    'upload_code' => 'activeForm:' . $this->activeForm->modelName . ':' . $this->activeForm->model->pk()
                ]);
            }
            $this->activeForm->model->{$this->colName} = implode(',', array_filter($file_ids));
        }
    }

}
