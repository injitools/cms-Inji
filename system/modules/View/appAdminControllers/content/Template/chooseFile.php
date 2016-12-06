<h1>Файлы темы <?= $template->config['template_name']; ?></h1>
<a href ='/admin/view/template/editFile/<?= $template->name; ?>?path=<?= $template->config['file']; ?>'>Основной файл темы</a>
<hr/>
<?php
foreach ($template->config['css'] as $file) {
    if (file_exists($template->path . '/css/' . $file)) {
        ?>
        <a href ='/admin/view/template/editFile/<?= $template->name; ?>?path=<?= 'css/' . $file; ?>'><?= $file; ?></a>
        <?php
    }
}
