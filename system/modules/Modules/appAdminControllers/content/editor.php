<?php

$table = new Ui\Table();
$table->name = 'Модели';
$table->addButton([
    'href' => "/admin/modules/createModel/{$module}",
    'text' => 'Создать'
]);
$table->setCols([
    'Модель',
    'Внешние изменения',
    'Операции'
]);
$modulePath = Module::getModulePath($module);
$path = $modulePath . '/models';
$config = Config::custom(App::$primary->path . '/modules/' . $module . '/generatorHash.php');
if (file_exists($path)) {
    $files = array_slice(scandir($path), 2);
    foreach ($files as $file) {
        if (is_dir($path . '/' . $file)) {
            continue;
        }
        $modelName = pathinfo($file, PATHINFO_FILENAME);
        $table->addRow([
            $modelName,
            (!empty($config['models/' . $file]) && $config['models/' . $file] == md5(file_get_contents($path . '/' . $file))) ? '<b class="text-success">Нету</b>' : '<b class="text-danger">Есть</b>',
            [
                'class' => 'actionTd',
                'html' => '<a class="btn btn-xs btn-success" href="/admin/modules/editModel/' . $module . '/' . $modelName . '"><i class="glyphicon glyphicon-edit"></i></a>'
                . ' <a class="btn btn-xs btn-danger" href="/admin/modules/delModel/' . $module . '/' . $modelName . '"><i class="glyphicon glyphicon-remove"></i></a>'
            ]
        ]);
    }
}
$table->draw();
$table = new Ui\Table();
$table->name = 'Контроллеры';
$table->addButton([
    'href' => "/admin/modules/createController/{$module}",
    'text' => 'Создать'
]);
$table->setCols([
    'Контроллер',
    'Тип',
    'Операции'
]);
$types = [
    'appControllers',
    'appAdminControllers',
    'Controllers'
];
foreach ($types as $type) {
    if (file_exists($modulePath . '/' . $type)) {
        $files = array_slice(scandir($modulePath . '/' . $type), 2);
        foreach ($files as $file) {
            $table->addRow([
                pathinfo($file, PATHINFO_FILENAME),
                $type,
                [
                    'class' => 'actionTd',
                    'html' => '<a class="btn btn-xs btn-success" href="/admin/modules/controllerEditor/' . $module . '/' . $type . '/' . pathinfo($file, PATHINFO_FILENAME) . '"><i class="glyphicon glyphicon-edit"></i></a>'
                    . ' <a class="btn btn-xs btn-danger" href="/admin/modules/delController/' . $module . '/' . $type . '/' . pathinfo($file, PATHINFO_FILENAME) . '"><i class="glyphicon glyphicon-remove"></i></a>'
                ]
            ]);
        }
    }
}
$table->draw();
