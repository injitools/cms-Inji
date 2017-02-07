<?php

return [
    'up' => function() {
        Materials\Material::createCol('date_publish');
        foreach (Materials\Material::getList() as $material) {
            $material->date_publish = $material->date_create;
            $material->save();
        }
    }
];
