<?php

/**
 * Migrations admin controller
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class MigrationsController extends adminController {

    public function manualAction() {
        if (!empty($_POST)) {
            $this->module->startMigration(\Migrations\Migration\Map::get($_POST['map'])->migration_id, $_POST['map'], $_FILES['file']['tmp_name']);
        }
        $selectArray = [
            '' => 'Выберите'
        ];
        $migrations = Migrations\Migration::getList();
        foreach ($migrations as $migration) {
            $item = [
                'text' => $migration->name,
                'input' => [
                    'name' => 'map',
                    'type' => 'select',
                    'source' => 'array',
                    'sourceArray' => ['' => 'Выберите']
                ]
            ];
            foreach ($migration->maps as $map) {
                $item['input']['sourceArray'][$map->id] = [
                    'text' => $map->name,
                    'input' => [
                        'type' => 'file',
                        'name' => 'file',
                        'noprefix' => true
                    ]
                ];
            }
            $selectArray[$migration->id] = $item;
        }
        $this->view->setTitle('Ручная миграции данных');
        $this->view->page(['data' => compact('selectArray')]);
    }

}
