<?php

/**
 * Dashboard controller
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class DashboardController extends adminController
{
    function indexAction()
    {
        $sections = $this->module->getSnippets('adminDashboardWidget');
        $extends = Module::getExtensions('Dashboard', 'snippets', 'adminDashboardWidget');
        $sections = array_merge($sections, $extends);
        $this->view->setTitle('Панель управления');
        $this->view->page(['data' => compact('sections')]);
    }

    function siteConfigAction()
    {
        if (isset($_POST['site_name'])) {
            $config = \App::$primary->config;
            $config['site']['name'] = $_POST['site_name'];
            $config['site']['email'] = $_POST['site_email'];
            $config['site']['keywords'] = $_POST['site_keywords'];
            $config['site']['description'] = $_POST['site_description'];
            if (!empty($_POST['metatags'])) {
                $config['site']['metatags'] = $_POST['metatags'];
            }
            if (!empty($_FILES['site_logo']['tmp_name'])) {
                $fileId = $this->Files->upload($_FILES['site_logo'], array('file_code' => 'site_logo'));
                $config['site']['site_logo'] = Files\File::get($fileId)->path;
            }
            Config::save('app', $config);
            Tools::redirect('/admin/dashboard/siteConfig', 'Изменения сохранены', 'success');
        }

        $this->view->setTitle('Общие настройки сайта');
        $this->view->page();
    }

}
