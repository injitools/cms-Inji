<?php

/**
 * Parser Item Offer Warehouse
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Exchange1c\Parser\Item\Offer;

class Warehouse extends \Migrations\Parser {

    public function parse() {
        if (is_array($this->data) && empty($this->data['@attributes'])) {
            foreach ($this->data as $warehouseCount) {
                $this->parseWarehouse($warehouseCount);
            }
        } elseif (is_array($this->data) && !empty($this->data['@attributes'])) {
            $this->parseWarehouse($this->data);
        }
    }

    function parseWarehouse($warehouseCount) {
        $count = $warehouseCount['@attributes']['КоличествоНаСкладе'];
        $objectId = \App::$cur->migrations->findObject((string)$warehouseCount['@attributes']['ИдСклада'], 'Ecommerce\Warehouse');
        if ($objectId) {
            $modelName = get_class($this->model);
            $warehouse = \Ecommerce\Item\Offer\Warehouse::get([[$modelName::index(), $this->model->pk()], [\Ecommerce\Warehouse::index(), $objectId->object_id]]);
            if (!$warehouse) {
                $warehouse = new \Ecommerce\Item\Offer\Warehouse([
                    $modelName::index() => $this->model->pk(),
                    \Ecommerce\Warehouse::index() => $objectId->object_id,
                    'count' => $count
                ]);
                $warehouse->save();
            } else {
                if ($warehouse->count != $count) {
                    $warehouse->count = $count;
                    $warehouse->save();
                }
            }
        }

    }

}
