<?php

/**
 * View app controller
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

/**
 * @property View $module
 */
class ViewController extends Controller {

    public function editorcssAction() {
        if (file_exists($this->view->template->path . '/css/editor.css')) {
            Tools::redirect('/static/templates/' . $this->view->template->name . '/css/editor.css');
        } else {
            header("Content-type: text/css");
            exit();
        }
    }

    public function checkStaticUpdatesAction($hash = '', $timeHash = '') {
        if (is_string($hash) && !empty($_GET['files'])) {
            $hashFiles = json_encode($_GET['files']);
            if (!empty($this->module->template->config['staticUpdaterSalt'])) {
                $hashFiles .= $this->module->template->config['staticUpdaterSalt'];
            }
            $hashFiles = md5($hashFiles);
            if ($hash !== $hashFiles) {
                exit();
            }
            $timeStr = '';
            $urls = [];
            foreach ($_GET['files'] as $href) {
                $path = App::$cur->staticLoader->parsePath($href);
                if (file_exists($path)) {
                    $urls[$href] = $path;
                    $timeStr .= filemtime($path);
                }
            }

            $timeMd5 = md5($timeStr);
            if ($timeHash === $timeMd5) {
                exit();
            }
            $cacheDir = Cache::getDir('static');
            $cssAll = '';
            if (!file_exists($cacheDir . 'all' . $timeMd5 . '.css')) {
                foreach ($urls as $primaryUrl => $url) {
                    $source = file_get_contents($url);
                    $rootPath = substr($primaryUrl, 0, strrpos($primaryUrl, '/'));
                    $levelUpPath = substr($rootPath, 0, strrpos($rootPath, '/'));
                    $source = preg_replace('!url\((\'?"?)[\.]{2}!isU', 'url($1' . $levelUpPath, $source);
                    $source = preg_replace('!url\((\'?"?)[\.]{1}!isU', 'url($1' . $rootPath, $source);
                    $source = preg_replace('#url\(([\'"]){1}(?!http|https|/|data\:)([^/])#isU', 'url($1' . $rootPath . '/$2', $source);
                    $source = preg_replace('#url\((?!http|https|/|data\:|\'|")([^/])#isU', 'url(' . $rootPath . '/$1$2', $source);
                    $cssAll .= $source . "\n";
                }
                file_put_contents($cacheDir . 'all' . $timeMd5 . '.css', $cssAll);
            }
            echo json_encode(['path' => '/' . $cacheDir . 'all' . $timeMd5 . '.css', 'timeHash' => $timeMd5]);
        }

    }

    public function templateProgramAction() {
        $args = func_get_args();
        if ($args) {
            $moduleName = ucfirst($args[0]);
            $params = array_slice($args, 1);
            if (file_exists($this->view->template->path . '/program/modules/' . $moduleName . '/' . $moduleName . '.php')) {
                include_once $this->view->template->path . '/program/modules/' . $moduleName . '/' . $moduleName . '.php';
                $module = new $moduleName($this->module->app);
                $cotroller = $module->findController();
                $cotroller->params = $params;
                $cotroller->run();
            }
        }
    }

}
