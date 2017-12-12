<?php

/**
 * Active form controller
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class ActiveFormController extends Controller {

    public function searchAction() {
        $result = new Server\Result();
        $searchString = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING);
        $searchString = trim(preg_replace('![^A-zА-я0-9@-_\. ]!iSu', ' ', urldecode($searchString)));
        if (!$searchString) {
            $result->content = [];
            $result->send();
        }
        try {
            $modelName = trim(filter_input(INPUT_GET, 'modelName', FILTER_SANITIZE_STRING));
            if (!$modelName) {
                throw new Exception('Не указана модель');
            }
            $model = new $modelName;
            if (!$model || !is_subclass_of($model, 'Model')) {
                throw new Exception('Модель не найдена');
            }
            $formName = trim(filter_input(INPUT_GET, 'formName', FILTER_SANITIZE_STRING));
            if (!$formName) {
                throw new Exception('Не указано название формы');
            }
            if (empty($modelName::$forms[$formName])) {
                throw new Exception('Не существует указанной формы');
            }
            $activeForm = new Ui\ActiveForm($model, $formName);
            $inputs = $activeForm->getInputs();
            $inputName = trim(filter_input(INPUT_GET, 'inputName', FILTER_SANITIZE_STRING));
            if (empty($inputs[$inputName])) {
                throw new Exception('У формы нет такого поля');
            }
        } catch (Exception $exc) {
            $result->success = false;
            $result->content = $exc->getMessage();
            return $result->send();
        }
        $options = [
            'where' => [
            ]
        ];

        $searchArr = [];
        foreach (explode(' ', $searchString) as $part) {
            $colWhere = [];
            $first = true;
            foreach ($inputs[$inputName]['cols'] as $col) {
                $part = trim($part);
                if ($part && strlen($part) > 2) {
                    $colWhere[] = [$col, '%' . $part . '%', 'LIKE', $first ? 'AND' : 'OR'];
                    $first = false;
                }
            }
            if ($colWhere) {
                $searchArr[] = $colWhere;
            }
        }
        if ($searchArr) {
            $options['where'][] = $searchArr;
        } else {
            $result->content = [];
            $result->send();
        }
        $list = $activeForm->getOptionsList($inputs[$inputName], ['noEmptyValue' => true], $modelName, 'aditional', $options,$model);
        $result->content = $list;
        $result->send();
    }

}
