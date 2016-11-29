<?php

$form = new Ui\Form();
$form->begin('Создание новой темы оформления');
$form->input('text', 'name', 'Название темы', ['value' => $template['template_name']]);
$form->input('hidden', 'map', '', ['value' => $template['map']]);
$this->widget('View\blockDrop', ['map' => $template['map']]);
$form->end('Сохранить', ['onclick' => 'blockDrop.submitMap(this);return false;']);
