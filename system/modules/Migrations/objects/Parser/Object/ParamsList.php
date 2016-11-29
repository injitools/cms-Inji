<?php

/**
 * Parser Object ParamsList
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Migrations\Parser\Object;

class ParamsList extends \Migrations\Parser {

    public function parse() {
        $walked = [];
        $params = \Migrations\Migration\Object\Param::getList(['where' => [
                        ['parent_id', $this->param->id],
                        ['object_id', $this->object->object->id],
        ]]);
        foreach ($params as $param) {
            if ($this->model) {
                if ($param->type == 'custom') {
                    $parserName = $param->value;
                } else {
                    $parserName = '\Migrations\Parser\Object\\' . ucfirst($param->type);
                }
                if (!\Tools::isAssoc($this->data)) {
                    foreach ($this->data as $data) {
                        $parser = new $parserName;
                        $parser->data = &$data;
                        $parser->param = $param;
                        $parser->model = $this->model;
                        $parser->object = $this->object;
                        $parser->parse();
                    }
                } else {
                    $parser = new $parserName;
                    $parser->data = &$this->data[$param->code];
                    $parser->param = $param;
                    $parser->model = $this->model;
                    $parser->object = $this->object;
                    $parser->parse();
                }
            }
            $walked[$param->code] = true;
        }
        //check unparsed params
        foreach ($this->data as $key => $data) {
            //skip parsed and attribtes
            if ($key == '@attributes' || !empty($walked[$key])) {
                continue;
            }
            $param = new \Migrations\Migration\Object\Param();
            $param->parent_id = $this->param->id;
            $param->object_id = $this->object->object->id;
            $param->code = $key;
            $param->type = 'param';
            $param->save();
        }
    }

    public function editor() {
        return [
            '' => 'Выберите',
            'param' => 'Параметр',
        ];
    }

}
