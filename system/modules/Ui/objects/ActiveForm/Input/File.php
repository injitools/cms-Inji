<?php

/**
 * Active form input files
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Ui\ActiveForm\Input;

class File extends \Ui\ActiveForm\Input {

  public function parseRequest($request) {
    if (!empty($_FILES[$this->activeForm->requestFormName]['tmp_name'][$this->modelName][$this->colName])) {
      $file_id = \App::$primary->files->upload([
          'tmp_name' => $_FILES[$this->activeForm->requestFormName]['tmp_name'][$this->modelName][$this->colName],
          'name' => $_FILES[$this->activeForm->requestFormName]['name'][$this->modelName][$this->colName],
          'type' => $_FILES[$this->activeForm->requestFormName]['type'][$this->modelName][$this->colName],
          'size' => $_FILES[$this->activeForm->requestFormName]['size'][$this->modelName][$this->colName],
          'error' => $_FILES[$this->activeForm->requestFormName]['error'][$this->modelName][$this->colName],
              ], [
          'upload_code' => 'activeForm:' . $this->activeForm->modelName . ':' . $this->activeForm->model->pk()
      ]);

      if ($file_id) {
        $this->activeForm->model->{$this->colName} = $file_id;
      }
    }
  }

}
