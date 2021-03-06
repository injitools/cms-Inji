<?php

/**
 * Parser Object Param
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Migrations\Parser\Object;

class Param extends \Migrations\Parser {

    public function parse() {
        $params = $this->param->childs;
        if (is_array($this->data) && !\Tools::isAssoc($this->data)) {
            foreach ($this->data as &$data) {
                $this->parseData($data, $params);
            }
        } else {
            $this->parseData($this->data, $params);
        }
    }

    /**
     * @param array $data
     * @param \Migrations\Migration\Object\Param[] $params
     */
    private function parseData(&$data, $params) {
        $objectParamValue = [
            'col' => '',
            'value' => ''
        ];
        $walked = [];
        foreach ($params as $param) {
            $objectParam = $data[$param->code];
            if ($this->model && $param->type) {
                switch ($param->type) {
                    case 'paramName':
                        $param->values(['key' => 'original']);
                        if (!isset($param->values(['key' => 'original'])[(string) $objectParam])) {
                            $valueObject = new \Migrations\Migration\Object\Param\Value();
                            $valueObject->param_id = $param->id;
                            $valueObject->original = (string) $objectParam;
                            $valueObject->save();
                        } else {
                            $objectParamValue['col'] = $param->values(['key' => 'original'])[(string) $objectParam]->replace;
                        }
                        break;
                    case 'paramValue':
                        if ($objectParamValue['col']) {
                            $modelName = get_class($this->model);
                            $col = $modelName::$cols[$objectParamValue['col']];
                            if ($col['type'] == 'select' && $col['source'] == 'relation') {
                                $relation = $modelName::getRelation($col['relation']);
                                $item = $relation['model']::get((string) $objectParam, 'name');
                                if (!$item) {
                                    $item = new $relation['model'];
                                    $item->name = (string) $objectParam;
                                    $item->save();
                                }
                                $objectParamValue['value'] = $item->id;
                            }
                        }
                        break;
                }
            }
            $walked[$param->code] = true;
        }
        if ($objectParamValue['col']) {
            $this->model->{$objectParamValue['col']} = $objectParamValue['value'];
        }
        //check unparsed params
        foreach ($data as $key => $item) {
            //skip parsed and attribtes
            if ($key == '@attributes' || !empty($walked[$key])) {
                continue;
            }
            $param = new \Migrations\Migration\Object\Param();
            $param->parent_id = $this->param->id;
            $param->object_id = $this->object->object->id;
            $param->code = $key;
            $param->save();
        }
    }

    public function editor() {
        return [
            '' => 'Выберите',
            'paramName' => 'Название параметра',
            'paramValue' => 'Значение параметра',
        ];
    }

}
