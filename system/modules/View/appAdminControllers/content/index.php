<?php

$table = new Ui\Table();
$table->name = 'Шаблоны';
$table->addButton([
    'text' => 'Создать шаблон',
    'href' => '/admin/view/createTemplate',
]);
$table->setCols([
    'Шаблон',
    '',
    ''
]);
if (!empty($templates['app']['installed']))
    foreach ($templates['app']['installed'] as $template => $name) {
        $table->addRow([
            $name,
            (empty($templates['app']['current']) || $templates['app']['current'] != $template) ? '<a href = "/admin/view/setDefault/' . $template . '">Установить по умолчанию</a>' : 'Тема по умолчанию',
            '<a href = "/admin/view/template/editFile/' . $template . '">Файлы</a> <a href = "/admin/view/editTemplate/' . $template . '">Редактировать</a>'
        ]);
    }
$table->draw();
