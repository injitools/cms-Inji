<div class="container-fluid">
    <div class="row panel">
      <?php
        if (!empty($_FILES['file'])) {
            App::$cur->files->upload($_FILES['file'], ['upload_code' => 'editorManager']);
        }
        $form = new Ui\Form();
        $form->begin();
        $form->input('file', 'file', 'Загрузить файл');
        echo '<div class="form-group"><button class ="btn btn-primary btn-sm">Загузить</button></div>';
        $form->end(false);
        ?>
    </div>
    <h2>Последние файлы</h2>
    <div class="row">
      <?php
        $files = Files\File::getList(['where' => ['upload_code', 'editorManager'], 'limit' => 12, 'order' => ['date_create', 'DESC']]);
        $i = 0;
        foreach ($files as $file) {
            ?>
            <div class="col-xs-6 col-sm-2 fileChooser" onclick="OpenFile('<?= $file->path; ?>');
                      return false;">
                <div class="thumbnail">
                  <?php
                    if ($file->type->group == 'image') {
                        echo "<img class='img-responsive' src ='{$file->path}?resize=200x200' />";
                    } else {
                        echo "<img class='img-responsive' src ='/static/moduleAsset/Files/images/formats/" . pathinfo($file->path, PATHINFO_EXTENSION) . ".png' />";
                    }
                    ?>

                    <?= $file->name; ?><br />
                    <small class="text-muted">
                        Оригинальное название:<br />
                        <?= $file->original_name; ?><br />
                    </small>
                </div>
            </div>
            <?php
            if ($i++ && !($i % 6)) {
                echo "</div><div class ='row'>";
            }
        }
        ?>
    </div>
</div>
<script>
    function GetUrlParam(paramName)
    {
      var oRegex = new RegExp('[\?&]' + paramName + '=([^&]+)', 'i');
      var oMatch = oRegex.exec(window.top.location.search);

      if (oMatch && oMatch.length > 1)
        return decodeURIComponent(oMatch[1]);
      else
        return '';
    }
    function OpenFile(fileUrl)
    {
      //PATCH: Using CKEditors API we set the file in preview window.	
      funcNum = GetUrlParam('CKEditorFuncNum');

      //fixed the issue: images are not displayed in preview window when filename contain spaces due encodeURI encoding already encoded fileUrl	
      window.top.opener.CKEDITOR.tools.callFunction(funcNum, fileUrl);
//	window.top.opener.SetUrl( encodeURI( fileUrl ).replace( '#', '%23' ) ) ;

      window.top.close();
      window.top.opener.focus();
    }
</script>
<style>
    .fileChooser:hover{
      cursor: pointer;
    }
</style>
