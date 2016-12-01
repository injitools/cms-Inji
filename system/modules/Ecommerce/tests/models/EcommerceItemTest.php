<?php

class EcommerceItemTest extends PHPUnit_Framework_TestCase {

    private $textParamId;
    private $listParamId;
    private $emptyParamId;

    /**
     * @covers Modules::install
     */
    public function setUp() {
        \App::$cur->Modules->install('Ecommerce');
        $this->textParam();
        $this->listParam();
        $this->emptyParam();
    }

    private function textParam() {
        $option = \Ecommerce\Item\Option::get('text', 'type');
        if (!$option) {
            $option = new \Ecommerce\Item\Option(['type' => 'text']);
            $option->save();
        }

        $param = \Ecommerce\Item\Param::get([['value', 'textParam'], ['item_option_id', $option->id]]);
        if (!$param) {
            $param = new \Ecommerce\Item\Param(['value' => 'textParam', 'item_option_id' => $option->id]);
            $param->save();
        }

        $this->textParamId = $param->id;
    }

    private function emptyParam() {
        $option = \Ecommerce\Item\Option::get('text', 'type');
        if (!$option) {
            $option = new \Ecommerce\Item\Option(['type' => 'text']);
            $option->save();
        }

        $param = \Ecommerce\Item\Param::get([['value', ''], ['item_option_id', $option->id]]);
        if (!$param) {
            $param = new \Ecommerce\Item\Param(['value' => '', 'item_option_id' => $option->id]);
            $param->save();
        }

        $this->emptyParamId = $param->id;
    }

    private function listParam() {
        $option = \Ecommerce\Item\Option::get('select', 'type');
        if (!$option) {
            $option = new \Ecommerce\Item\Option(['type' => 'select']);
            $option->save();
        }
        $option->id;
        $item = \Ecommerce\Item\Option\Item::get([['item_option_id', $option->id], ['value', 'success']]);
        if (!$item) {
            $item = new \Ecommerce\Item\Option\Item([
                'item_option_id' => $option->id,
                'value' => 'success'
            ]);
            $item->save();
        }

        $param = \Ecommerce\Item\Param::get([['value', $item->id], ['item_option_id', $option->id]]);
        if (!$param) {
            $param = new \Ecommerce\Item\Param(['value' => $item->id, 'item_option_id' => $option->id]);
            $param->save();
        }

        $this->listParamId = $param->id;
    }

    /**
     * @covers Ecommerce\Item\Param::valueText
     */
    public function testTextParam() {
        $param = \Ecommerce\Item\Param::get($this->textParamId);
        $this->assertEquals('textParam', $param->valueText());
    }

    /**
     * @covers Ecommerce\Item\Param::valueText
     */
    public function testListParam() {
        $param = \Ecommerce\Item\Param::get($this->listParamId);
        $this->assertEquals('success', $param->valueText());
    }

    /**
     * @covers Ecommerce\Item\Param::valueText
     */
    public function testEmptyParam() {
        $param = \Ecommerce\Item\Param::get($this->emptyParamId);
        $this->assertEquals('empty', $param->valueText('empty'));
    }
}