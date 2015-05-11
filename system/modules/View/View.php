<?php

class View extends Module {

    public $title = 'Title';
    public $template = ['name' => 'default'];
    public $libAssets = array('css' => array(), 'js' => array());
    public $dynAssets = array('css' => array(), 'js' => array());
    public $viewedContent = '';
    public $contentData = [];
    public $templatesPath = '';
    private $tmp_data = array();

    function init() {
        if (!empty(Inji::app()->Config->app['site']['name'])) {
            $this->title = Inji::app()->Config->site['site']['name'];
        }
        if (!empty($this->config[Inji::app()->curApp['type']]['current'])) {
            $this->template['name'] = $this->config[Inji::app()->curApp['type']]['current'];
        }
        $this->templatesPath = Inji::app()->curApp['path'] . "/templates";
        $template = $this->getConfig($this->template['name']);
        if ($template) {
            $this->template = $template;
            $this->template['path'] = $this->templatesPath . '/' . $this->template['name'];
        } else {
            $this->template = [
                'name' => 'default',
                'file' => 'index.html',
                'path' => $this->templatesPath . '/default'
            ];
        }
        $this->tmp_data = array(
            'path' => $this->templatesPath . "/{$this->template['name']}/{$this->template['file']}",
            'name' => $this->template['name'],
            'module' => Inji::app()->curModule,
        );
    }

    function getConfig($templateName) {
        return Inji::app()->config->custom($this->templatesPath . "/{$templateName}/config.php");
    }

    function page($params = []) {
        $this->tmp_data['contentPath'] = Inji::app()->curController->path . '/content';
        $this->tmp_data['content'] = Inji::app()->curController->method;
        $data = $this->paramsParse($params);
        if (file_exists($data['path'])) {
            $source = file_get_contents($data['path']);
            $this->parseSource($source);
        } else {
            $this->content();
        }
    }

    function paramsParse($params) {
        if (!$this->tmp_data['module']) {
            $this->tmp_data['module'] = Inji::app()->curModule;
        }
        $data = $this->tmp_data;
        // set template
        if (!empty($params['template'])) {
            if (file_exists($this->template['path'] . "/{$params['template']}.html")) {
                $data['file'] = $params['template'] . '.html';
                $data['path'] = $this->template['path'] . '/' . $data['file'];
            } elseif ($template = $this->getConfig($params['template'])) {
                $this->template = $template;
                $data['path'] = $this->template['path'] = $this->templatesPath . '/' . $this->template['name'] . '/' . $this->template['file'];
                $data['name'] = $this->template['name'];
                $data['file'] = $this->template['file'];
            }
        }
        //set module
        if (!empty($params['module'])) {
            $data['module'] = Inji::app()->$params['module'];
        }
        //set content
        if (!empty($params['content'])) {

            $paths = [
                'template' => $this->templatesPath . '/' . $this->template['name'] . "/modules/{$data['module']->moduleName}",
                'controlelrContent' => Inji::app()->curController->path . '/content'
            ];

            foreach ($paths as $type => $path) {
                if (file_exists($path . '/' . $params['content'] . '.php')) {
                    $data['contentPath'] = $path;
                    $data['content'] = $params['content'];
                    break;
                }
            }
        }
        if (!empty($params['data'])) {
            $this->contentData = array_merge($this->contentData, $params['data']);
        }
        $this->tmp_data = $data;
        return $data;
    }

    function content($params = []) {
        $this->current_function = 'CONTENT';
        if (Inji::app()->msg && empty($this->template['noSysMesAutoShow'])) {
            Inji::app()->msg->show(true);
        }

        $_params = $this->paramsParse($params);
        extract($this->contentData);
        if (!file_exists($_params['contentPath'] . '/' . $_params['content'] . '.php')) {
            echo 'Content not found';
        } else {
            include $_params['contentPath'] . '/' . $_params['content'] . '.php';
        }
    }

    private function parseRaw($source) {
        if (!$source)
            return array();

        preg_match_all("|{([^}]+)}|", $source, $result);
        return $result[1];
    }

    function parseSource($source) {
        $tags = $this->parseRaw($source);
        foreach ($tags as $rawTag) {
            $tag = explode(':', $rawTag);
            switch ($tag[0]) {
                case 'CONTENT':
                    $source = $this->cutTag($source, $rawTag);
                    $this->content();
                    break;
                case 'WIDGET':
                    $source = $this->cutTag($source, $rawTag);
                    $this->widget($tag[1], ['params' => array_slice($tag, 2)]);
                    break;
                case 'HEAD':
                    $source = $this->cutTag($source, $rawTag);
                    $this->head();
                    break;
                case 'PAGE':
                    $source = $this->cutTag($source, $rawTag);
                    $this->page(['template' => $tag[1]]);
                    break;
            }
        }
        echo $source;
    }

    function cutTag($source, $rawTag) {
        $pos = strpos($source, $rawTag) - 1;
        echo substr($source, 0, $pos);
        return substr($source, ( $pos + strlen($rawTag) + 2));
    }

    function head() {

        echo "<title>{$this->title}</title>\n";

        if (!empty($this->template['favicon']) && file_exists($this->template['path'] . "/{$this->template['favicon']}"))
            echo "        <link rel='shortcut icon' href='/templates/{$this->template['name']}/{$this->template['favicon']}' />";
        elseif (file_exists(Inji::app()->curApp['path'] . '/static/images/favicon.ico'))
            echo "        <link rel='shortcut icon' href='/static/images/favicon.ico' />";


        if (!empty(Inji::app()->Config->app['site']['keywords'])) {
            echo "\n        <meta name='keywords' content='" . Inji::app()->Config->site['site']['keywords'] . "' />";
        }
        if (!empty(Inji::app()->Config->app['site']['description'])) {
            echo "\n        <meta name='description' content='" . Inji::app()->Config->site['site']['description'] . "' />";
        }
        if (!empty(Inji::app()->Config->app['site']['metatags'])) {
            foreach (Inji::app()->Config->app['site']['metatags'] as $meta)
                echo "\n        <meta name='{$meta['name']}' content='{$meta['content']}' />";
        }



        if (!empty($this->libAssets['css'])) {
            foreach ($this->libAssets['css'] as $css) {
                if (strpos($css, '//') !== false)
                    $href = $css;
                else
                    $href = $css;
                echo "\n        <link href='{$href}' rel='stylesheet' type='text/css' />";
            }
        }
        if (!empty($this->template['css'])) {
            foreach ($this->template['css'] as $css) {
                if (strpos($css, '://') !== false)
                    $href = $css;
                else
                    $href = Inji::app()->curApp['templates_path'] . "/{$this->template['name']}/css/{$css}";
                echo "\n        <link href='{$href}' rel='stylesheet' type='text/css' />";
            }
        }
        if (!empty($this->dynAssets['css'])) {
            foreach ($this->dynAssets['css'] as $css) {
                if (strpos($css, '//') !== false)
                    $href = $css;
                else
                    $href = $css;
                echo "\n        <link href='{$href}' rel='stylesheet' type='text/css' />";
            }
        }
        if (!empty($this->libAssets['js'])) {
            foreach ($this->libAssets['js'] as $js) {
                if (is_string($js)) {
                    $href = $js;
                } elseif (strpos($js['file'], '//') !== false)
                    $href = $js['file'];
                elseif ($js['template'])
                    $href = Inji::app()->curApp['templates_path'] . "/{$this->template['name']}/js/{$js['file']}";
                else
                    $href = $js['file'];
                echo "\n        <script src='{$href}'></script>";
            }
        }
        if (!empty($this->template['js'])) {
            foreach ($this->template['js'] as $js) {
                if (strpos($js, '://') !== false)
                    $href = $js;
                else
                    $href = Inji::app()->curApp['templates_path'] . "/{$this->template['name']}/js/{$js}";
                echo "\n        <script src='{$href}'></script>";
            }
        }
        if (!empty($this->dynAssets['js'])) {
            foreach ($this->dynAssets['js'] as $js) {
                if (is_string($js)) {
                    $href = $js;
                } elseif (strpos($js['file'], '//') !== false)
                    $href = $js['file'];
                elseif ($js['template'])
                    $href = Inji::app()->curApp['templates_path'] . "/{$this->template['name']}/js/{$js['file']}";
                else
                    $href = $js['file'];
                echo "\n        <script src='{$href}'></script>";
            }
        }
    }

    function timegen() {
        $this->current_function = 'TIMEGEN';
        echo round(( microtime(true) - INJI_TIME_START), 4);
    }

    function customAsset($type, $href, $lib = false) {
        if (!$lib) {
            $this->dynAssets[$type][] = $href;
        } else {
            $this->libAssets[$type][] = $href;
        }
    }

    function setTitle($title, $add = true) {
        if ($add && !empty(Inji::app()->Config->app['site']['name'])) {
            $this->title = $title . ' - ' . Inji::app()->Config->app['site']['name'];
        } else {
            $this->title = $title;
        }
    }

    function widget($widgetName, $params = []) {
        if ($params && is_array($params)) {
            extract($params);
        }
        if (strpos($widgetName, '\\')) {
            $widgetName = explode('\\', $widgetName);
            if (Inji::app()->$widgetName[0] && file_exists(Inji::app()->$widgetName[0]->path . '/widgets/' . $widgetName[1] . '.php')) {
                include Inji::app()->$widgetName[0]->path . '/widgets/' . $widgetName[1] . '.php';
            }
        } elseif (file_exists($this->templatesPath . '/' . $this->template['name'] . '/widgets/' . $widgetName . '.php')) {
            include $this->templatesPath . '/' . $this->template['name'] . '/widgets/' . $widgetName . '.php';
        } elseif (file_exists(Inji::app()->curApp['path'] . '/widgets/' . $widgetName . '.php')) {
            include Inji::app()->curApp['path'] . '/widgets/' . $widgetName . '.php';
        }
    }

}

?>
