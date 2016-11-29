<?php

$form = new Ui\Form();
$form->begin('Создание новой темы оформления');
$form->input('text', 'name', 'Название темы');
$form->input('hidden', 'map');
$this->widget('View\blockDrop');
$form->end('Создать', ['onclick' => 'blockDrop.submitMap(this);return false;']);
