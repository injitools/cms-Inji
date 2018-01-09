<?php
namespace Inji\Setup\Main;
use Inji\Config;
use Inji\Controller;
use Inji\Tools;

/**
 * setup controller
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class MainAppSetupController extends Controller {

    public function indexAction() {
        $this->view->setTitle('Система');
        $this->view->page();
    }

    public function enterAction() {
        $config = Config::share();
        if (!empty($_POST['systemPass'])) {
            if (empty($config['systemPass'])) {
                $config['systemPass'] = password_hash($_POST['systemPass'], PASSWORD_DEFAULT);
                $config['installed'] = true;
                Config::save('share', $config);
            }
            if (password_verify($_POST['systemPass'], $config['systemPass'])) {
                $_SESSION['systemLogin'] = 1;
            } else {
                if (empty($config['failTry'])) {
                    $config['failTry'] = 1;
                } else {
                    $config['failTry']++;
                }
                Config::save('share', $config);
            }
            Tools::redirect('/setup');
        }
        if (!empty($config['systemPass']) && !empty($_COOKIE['systemPass']) && $_COOKIE['systemPass'] == $config['systemPass']) {
            Tools::redirect('/setup');
        }
        $this->view->setTitle('Enter');
        $this->view->page();
    }

}
