<?php

return [
    'up' => function () {
        $field = new \Ecommerce\Delivery\Field(
            [
                'name' => 'Город',
                'type' => 'search',
                'required' => 1,
                'save' => 1,
                'options' => json_encode([
                    'type' => 'search',
                    'source' => 'relation',
                    'relation' => 'fieldItems',
                    'label' => 'Город',
                    'cols' => ['value'],
                    'col' => 'id',
                ])
            ]
        );
        $field->save();
        set_time_limit(0);
        $handle = fopen(__DIR__ . '/../vendor/CdekCity_RUS_20170729.csv', 'r');
        $row = 1;
        $cols = [];
        while (($data = fgetcsv($handle, 10000, ";")) !== FALSE) {
            if ($row === 1) {
                $cols = $data;
            } else {
                $item = new \Ecommerce\Delivery\Field\Item([
                    'delivery_field_id' => $field->id,
                    'value' => $data[1],
                    'data' => json_encode([
                        $cols[0] => $data[0],
                        $cols[1] => $data[1],
                        $cols[2] => $data[2],
                        $cols[3] => $data[3],
                        $cols[4] => $data[4],
                        $cols[5] => $data[5],
                        $cols[6] => $data[6],
                        $cols[7] => $data[7],
                    ])
                ]);
                $item->save();
            }
            $row++;
        }
    }
];
