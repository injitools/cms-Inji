<h1>Общие настройки сайта</h1>
<form action = '' method = 'POST' enctype="multipart/form-data">
    <div class ="form-group">
        <label>Название сайта</label>
        <input class ="form-control" type = 'text' name = 'site_name' value = '<?php if (!empty(\App::$primary->config['site']['name'])) echo \App::$primary->config['site']['name']; ?>' />
    </div>
    <div class ="form-group">
        <label>Название компании</label>
        <input class ="form-control" type = 'text' name = 'company_name' value = '<?php if (!empty(\App::$primary->config['site']['company_name'])) echo \App::$primary->config['site']['company_name']; ?>' />
    </div>
    <div class ="form-group">
        <label>Основной домен</label>
        <input class ="form-control" type = 'text' name = 'site_domain' value = '<?php if (!empty(\App::$primary->config['site']['domain'])) echo \App::$primary->config['site']['domain']; ?>' />
    </div>
    <div class ="form-group">
        <label>Контактный email</label>
        <input class ="form-control" type = 'text' name = 'site_email' value = '<?php if (!empty(\App::$primary->config['site']['email'])) echo \App::$primary->config['site']['email']; ?>' />
    </div>
    <div class ="form-group">
        <label>Ключевые слова</label>
        <input class ="form-control" type = 'text' name = 'site_keywords' value = '<?php if (!empty(\App::$primary->config['site']['keywords'])) echo \App::$primary->config['site']['keywords']; ?>' />
    </div>
    <div class ="form-group">
        <label>Краткое описание сайта</label>
        <input class ="form-control" type = 'text' name = 'site_description' value = '<?php if (!empty(\App::$primary->config['site']['description'])) echo \App::$primary->config['site']['description']; ?>' />
    </div>
    <?php
    $form = new Ui\Form();
    $form->input('image', 'site_logo', 'Лого сайта', ['value' => !empty(\App::$primary->config['site']['site_logo']) ? \App::$primary->config['site']['site_logo'] : '']);
    ?>
    <h2>Дополнительные мета теги</h2>
    <div class ="form-group">
        <button type = 'button' class = 'btn btn-primary ' onclick = 'addMeta()'>Добавить <i class = 'glyphicon glyphicon-plus'></i></button>
    </div>
    <table class = 'table table-striped metatable'>
        <tr><th>name</th><th>content</th><th></th></tr>
        <?php
        $i = 0;
        if (!empty(\App::$primary->config['site']['metatags'])) {
            foreach (\App::$primary->config['site']['metatags'] as $meta) {
                echo "<tr>"
                . "<td><input type ='text' name ='metatags[{$i}][name]' value = '{$meta['name']}' class ='form-control' /></td>"
                . "<td><input type ='text' name ='metatags[{$i}][content]' value = '{$meta['content']}' class ='form-control' /></td>"
                . "<td><button type = 'button' class='btn btn-danger btn-sm delproove' onclick = '$(this).parent().parent().remove();'><i class='glyphicon glyphicon-remove'></i></button></td>"
                . "</tr>";
                $i++;
            }
        }
        ?>
    </table>
    <script>
        var metaI = <?= $i; ?>;
        function addMeta() {
          $(".metatable").append("<tr>\n\
    <td><input type ='text' name ='metatags[" + (++metaI) + "][name]' class ='form-control' /></td>\n\
  <td><input type ='text' name ='metatags[" + (metaI) + "][content]' class ='form-control' /></td>\n\
  <td><button type = 'button' class='btn btn-danger btn-sm delproove' onclick = '$(this).parent().parent().remove();'><i class='glyphicon glyphicon-remove'></i></button></td>\n\
  </tr>");
        }
    </script>
    <div class ="form-group">
        <button class ='btn btn-success'>Сохранить</button>
    </div>
</form>
