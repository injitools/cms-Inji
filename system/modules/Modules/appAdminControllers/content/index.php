<?php

$table = new Ui\Table();
$table->name = 'Установленные модули';
$table->addButton(['href'=>'/admin/modules/create','text'=>'Создать']);
$table->addButton(['href'=>'/admin/modules/install','text'=>'Установить']);
$table->setCols([
    'Модуль',
    'Панель администратора',
    'Публичная часть',
    'Управление'
]);
foreach (App::$primary->config['modules'] as $module) {
    $info = Module::getInfo($module);
    $table->addRow([
        $info['name'],
        '',
        '',
        ''
    ]);
}

$table->draw();


//$systemModules = array_slice(scandir(INJI_SYSTEM_DIR . '/modules'), 2);