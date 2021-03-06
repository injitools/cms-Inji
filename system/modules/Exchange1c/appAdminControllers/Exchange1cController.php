<?php

/**
 * Exchange1c admin controller
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class Exchange1cController extends adminController {

    public function reExchangeAction() {
        error_reporting(E_ALL);
        ini_set('error_reporting', E_ALL);
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        ini_set('memory_limit', '-1');
        ignore_user_abort(true);
        set_time_limit(0);
        Model::$logging = false;
        App::$cur->log->run = true;
        App::$cur->log->forceView = true;
        $reExchange = Exchange1c\Exchange::get((int) $_GET['item_pk']);

        $exchange = new \Exchange1c\Exchange();
        $exchange->type = $reExchange->type;
        $exchange->path = $reExchange->path;
        $exchange->save();

        foreach ($reExchange->files as $reFile) {
            if (strpos($reFile->name, '/')) {
                Tools::createDir($exchange->path . '/' . substr($reFile->name, 0, strrpos($reFile->name, '/')));
            }
            copy($reExchange->path . '/' . $reFile->name, $exchange->path . '/' . $reFile->name);
        }

        foreach ($reExchange->logs as $reLog) {
            if (!in_array($reLog->info, ['import'])) {
                continue;
            }
            $_GET = json_decode($reLog->query, true);

            $log = new \Exchange1c\Exchange\Log();
            $log->exchange_id = $exchange->id;
            $log->type = 'mode';
            $log->info = $reLog->info;
            $log->status = 'process';
            $log->query = $reLog->query;
            $log->save();

            $modeClass = 'Exchange1c\Mode\\' . ucfirst(strtolower($log->info));
            if (!class_exists($modeClass)) {
                $log->status = 'failure';
                $log->info = 'mode class ' . $modeClass . ' not found';
                $log->date_end = date('Y-m-d H:i:s');
                $log->save();
            }
            $mode = new $modeClass;
            $mode->exchange = $exchange;
            $mode->log = $log;
            $key = \App::$cur->log->start('start migration');
            $mode->process();
            \App::$cur->log->end($key);
        }
        echo '<hr /><a href="/admin/exchange1c/Exchange">Назад</a>';
        Model::$logging = true;
    }

    public function findOldAction() {
        if (!empty($_POST['ids'])) {
            $countIds = 0;
            $countModels = 0;
            foreach ($_POST['ids'] as $id) {
                $id = \Migrations\Id::get($id);
                if (!$id) {
                    continue;
                }
                $modelName = $id->type;
                $model = $modelName::get($id->object_id);
                if ($model) {
                    $countModels++;
                    $model->delete();
                }
                $countIds++;
                $id->delete();
            }
            if ($countModels) {
                Msg::add("Удалено {$countModels} объектов с сайта");
            }
            if ($countIds) {
                Msg::add("Удалено {$countIds} объектов миграции");
            }
        }
        $this->view->setTitle('Объекты миграции не обновляемые при обмене');
        $this->view->page();
    }

}