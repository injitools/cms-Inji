<?php

/**
 * Main module
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class Main extends Module {

    public function init() {
        $config = Config::share();
        if (!empty($config['failTry']) && $config['failTry'] > 3) {
            exit('Превышен лимит неправильного ввода пароля. Для разблокировки отредактируйте файл %INJI_PROGRAM_DIR%/config/config.php');
        }
        if (empty(App::$cur->params[0]) || App::$cur->params[0] != 'enter') {
            if (empty($config['systemPass'])) {
                Tools::redirect('/setup/enter', 'Придумайте системный пароль');
            }
            if (empty($_SESSION['systemLogin'])) {
                Tools::redirect('/setup/enter', 'Введите системный пароль');
            }
        }
    }

}
