<?php

class ViewController extends Controller
{
    function indexAction()
    {
        $templates = App::$primary->view->config;
        App::$cur->view->setTitle('Шаблоны сайта');
        App::$cur->view->page(['data' => compact('templates')]);
    }

    function setDefaultAction($name)
    {
        $templates = App::$primary->view->config;
        $templates['app']['current'] = $name;
        Config::save('module', $templates, 'View', App::$primary);
        Tools::redirect('/admin/View');
    }

    function createTemplateAction()
    {
        $this->view->setTitle('Создание шаблона');
        App::$cur->view->customAsset('css', '/static/moduleAsset/View/css/blockDrop.css');
        App::$cur->view->customAsset('js', ['file' => '/static/moduleAsset/View/js/blockDrop.js', 'libs' => ['jquery-ui']]);
        if (!empty($_POST)) {
            $text = '<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        {HEAD}
    </head>
    <body>
    {PAGE:map}
    </body>
</html>';
            $templates = App::$primary->view->config;
            $templates['app']['installed'][$_POST['name']] = $_POST['name'];
            Config::save('module', $templates, 'View', App::$primary);
            $path = App::$primary->path . '/templates/' . $_POST['name'] . '/index.html';
            $pathMap = App::$primary->path . '/templates/' . $_POST['name'] . '/map.html';
            Tools::createDir(App::$primary->path . '/templates/' . $_POST['name']);
            file_put_contents($path, $text);
            file_put_contents($pathMap, trim($_POST['map']));
            $template = [
                'template_name' => $_POST['name'],
                'name' => $_POST['name'],
                'file' => 'index.html',
            ];
            Config::save(App::$primary->path . '/templates/' . $_POST['name'] . '/config.php', $template);
            Tools::redirect('/admin/View');
        }
        $this->view->page();
    }

    function editTemplateAction($templateName)
    {
        $this->view->setTitle('Редактирование шаблона');
        App::$cur->view->customAsset('css', '/static/moduleAsset/View/css/blockDrop.css');
        App::$cur->view->customAsset('js', '/static/moduleAsset/View/js/blockDrop.js');
        $template = Config::custom(App::$primary->path . '/templates/' . $templateName . '/config.php');
        $pathMap = App::$primary->path . '/templates/' . $templateName . '/map.html';
        if (!empty($_POST)) {
            $templates = App::$primary->view->config;
            $templates['app']['installed'][$templateName] = $_POST['name'];
            Config::save('module', $templates, 'View', App::$primary);

            file_put_contents($pathMap, trim($_POST['map']));

            $template['template_name'] = $templateName;
            $template['name'] = $templateName;
            Config::save(App::$primary->path . '/templates/' . $_POST['name'] . '/config.php', $template);
            Tools::redirect('/admin/View');
        }
        $template['map'] = file_get_contents($pathMap);
        $this->view->page(['data' => compact('template')]);
    }

}

?>