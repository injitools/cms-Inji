<h1>Файлы темы <?= $template->config['template_name']; ?></h1>
<h4>HTML файлы</h4>
<a href='/admin/view/template/editFile/<?= $template->name; ?>?path=<?= $template->config['file']; ?>'>Основной файл
    темы</a>
<?php
if (!empty($template->config['files']['aditionTemplateFiels'])) {
    foreach ($template->config['files']['aditionTemplateFiels'] as $file) {
        if (file_exists($template->path . '/' . $file['file'] . '.html')) {
            echo "<a href='/admin/view/template/editFile/{$template->name}?path={$file['file']}.html'>{$file['name']}</a> ";
        }
    }
}
?>
<h4>CSS файлы</h4>
<?php
if (!empty($template->config['css'])) {
    foreach ($template->config['css'] as $file) {
        if (file_exists($template->path . '/css/' . $file)) {
            echo "<a href='/admin/view/template/editFile/{$template->name}?path=css/{$file}'>{$file}</a> ";
        }
    }
}
?>
<h4>JS файлы</h4>
<?php
if (!empty($template->config['js'])) {
    foreach ($template->config['js'] as $file) {
        if (file_exists($template->path . '/js/' . $file)) {
            echo "<a href='/admin/view/template/editFile/{$template->name}?path=js/{$file}'>{$file}</a> ";
        }
    }
}
