<?php

/**
 * Migrations public controller
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2016 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class MigrationsController extends Controller {

    public function exportAction($migrationName = '', $mapName = '', $secret = '') {
        $migration = \Migrations\Migration::get($migrationName, 'alias');
        if (!$migration) {
            echo 'migration not select';
            return;
        }
        if ($migration->secret != $secret) {
            echo 'security error';
            return;
        }
        $maps = $migration->maps(['key' => 'alias']);
        if (empty($maps[$mapName])) {
            echo 'map not select';
            return;
        }
    }
}