<?php

/**
 * StaticLoader class
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class StaticLoader extends Module {

    public $mimes = [];

    function init() {
        $this->mimes = $this->config['mimes'];
    }

    function parsePath($path) {
        $path = Tools::parsePath($path);

        if (strpos($path, '/') === 0) {
            $path = substr($path, 1);
        }
        $scriptApp = \App::$cur;
        $app = substr($path, 0, strpos($path, '/'));
        if (file_exists(INJI_SYSTEM_DIR . '/program/' . $app)) {
            $path = substr($path, strpos($path, '/') + 1);
            $scriptApp = new App();
            $scriptApp->name = $app;
            $scriptApp->system = true;
            $scriptApp->staticPath = "/" . $scriptApp->name . "/static";
            $scriptApp->templatesPath = "/" . $scriptApp->name . "/static/templates";
            $scriptApp->path = INJI_SYSTEM_DIR . '/program/' . $scriptApp->name;
            $scriptApp->type = 'app' . ucfirst(strtolower($scriptApp->name));
            $scriptApp->installed = true;
            $scriptApp->params = [];
            $scriptApp->config = Config::app($scriptApp);
        }

        if (strpos($path, 'static/') !== false && strpos($path, 'static/') <= 1) {
            $path = substr($path, strpos($path, 'static') + 7);
        }
        $type = substr($path, 0, strpos($path, '/'));

        switch ($type) {
            case 'templates':
                $path = substr($path, strpos($path, '/') + 1);
                return $scriptApp->view->templatesPath . '/' . $path;
            case 'system':
                $path = substr($path, strpos($path, '/') + 1);
                return INJI_SYSTEM_DIR . '/static/' . $path;
            case 'moduleAsset':
                $path = substr($path, strpos($path, '/') + 1);
                if (!strpos($path, '/')) {
                    return false;
                }
                $module = substr($path, 0, strpos($path, '/'));

                if (!$scriptApp->$module) {
                    return false;
                }
                $path = substr($path, strpos($path, '/') + 1);
                return $scriptApp->$module->path . '/static/' . $path;
            default:
                return $scriptApp->path . '/static/' . $path;
        }
    }

    function header($code, $exit = false) {
        switch ($code) {
            case '404':
                header('HTTP/1.1 404 Not Found');
                break;
            default :
                header($code);
        }
        if ($exit) {
            exit;
        }
    }

    function giveFile($file) {
        if (!file_exists($file)) {
            $this->header(404, true);
        }


        $fileinfo = pathinfo($file);
        if (empty($fileinfo['extension'])) {
            header('HTTP/1.1 404 Not Found');
            exit();
        }

        if (!empty($_GET['resize'])) {

            $allow_resize = false;
            if (App::$cur->db) {
                $type = Files\Type::get($fileinfo['extension'], 'ext');
                $allow_resize = $type->allow_resize;
            }
            if (empty($type) && in_array(strtolower($fileinfo['extension']), array('png', 'jpg', 'jpeg', 'gif'))) {
                $allow_resize = true;
            }
            if ($allow_resize) {

                $sizes = explode('x', $_GET['resize']);
                $sizes[0] = intval($sizes[0]);
                if (isset($sizes[1]))
                    $sizes[1] = intval($sizes[1]);
                else
                    $sizes[1] = 0;

                if (!$sizes[0] || !$sizes[1]) {
                    header('HTTP/1.1 404 Not Found');
                    exit();
                } elseif ($sizes[0] > 2000 || $sizes[1] > 2000) {
                    header('HTTP/1.1 404 Not Found');
                    exit();
                } else {
                    $dir = App::$primary->path;

                    if (!empty($_GET['resize_crop'])) {
                        if (in_array($_GET['resize_crop'], array('q', 'c')))
                            $crop = $_GET['resize_crop'];
                        else
                            $crop = 'c';
                    }
                    elseif (!empty($_GET['resize_quadro']))
                        $crop = 'q';
                    else
                        $crop = '';
                    $pos = 'center';
                    if (!empty($_GET['resize_pos']) && in_array($_GET['resize_pos'], array('top', 'center'))) {
                        $pos = $_GET['resize_pos'];
                    }
                    $dirnoslash = str_replace('/', '', substr($fileinfo['dirname'], strpos($fileinfo['dirname'], '/static')));
                    $path = $dir . '/static/cache/' . $dirnoslash . $fileinfo['filename'] . '.' . $sizes[0] . 'x' . $sizes[1] . $crop . $pos . '.' . $fileinfo['extension'];
                    if (!file_exists($path)) {
                        Tools::createDir($dir . '/static/cache/');
                        copy($file, $path);
                        Tools::resizeImage($path, $sizes[0], $sizes[1], $crop, $pos);
                    }

                    $file = $path;
                }
            }
        }

        $request = getallheaders();
        if (isset($request['If-Modified-Since'])) {
            // Разделяем If-Modified-Since (Netscape < v6 отдаёт их неправильно)
            $modifiedSince = explode(';', $request['If-Modified-Since']);

            // Преобразуем запрос клиента If-Modified-Since в таймштамп
            $modifiedSince = strtotime($modifiedSince[0]);
        } else {
            // Устанавливаем время модификации в ноль
            $modifiedSince = 0;
        }

        header("Cache-control: public");
        header("Accept-Ranges: bytes");
        header("Pragma: public");
        header("Content-Length: " . filesize($file));
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($file)) . ' GMT');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 60 * 60 * 24) . ' GMT');
        if (filemtime($file) <= $modifiedSince && (!isset($_SERVER['HTTP_CACHE_CONTROL']) || $_SERVER['HTTP_CACHE_CONTROL'] != 'no-cache')) {
            // Разгружаем канал передачи данных!
            header('HTTP/1.1 304 Not Modified');
            exit();
        }

        //if( strpos( $file, '/static/doc' ) !== false ) {
        header('Content-Description: File Transfer');
        //}
        if (isset($this->mimes[strtolower($fileinfo['extension'])])) {
            header("Content-Type: " . $this->mimes[strtolower($fileinfo['extension'])]);
        }
        if (isset($_GET['frustrate_dl']) || in_array($fileinfo['extension'], array('pptx'))) {
            header('Content-Disposition: attachment; filename=' . basename($file));
        }

        header('Content-Transfer-Encoding: binary');
        //}
        if (in_array($fileinfo['extension'], array('doc', 'docx', 'xls', 'xlsx')))
            header("Content-Disposition: attachment; filename=" . $file);



        readfile($file);
        exit();
    }

}
