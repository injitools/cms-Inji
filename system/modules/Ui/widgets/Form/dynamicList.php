<div class="dynamicList">
    <h3>
        <div class="pull-right">
          <?= $options['source'] != 'relation' || !empty($options['modelPk']) ? '<a class="btn btn-primary btn-xs" onclick="inji.Ui.forms.addRowToList(this);">Добавить</a>' : ''; ?>
        </div>
        <?= $label; ?>
    </h3>
    <?php
    if ($options['source'] == 'relation' && empty($options['modelPk'])) {
        echo '<h4 class=" text-muted">Чтобы добавить связи, сначала создайте объект</h4>';
        echo '<p class=" text-muted">Просто заполните доступные поля и нажмите кнопку внизу формы. После этого дополнительные поля разблокируются</p>';
    } else {
        ?>
        <div class="table-responsive">
            <table class ='table table-striped'>
                <thead>
                    <tr>
                      <?php
                      foreach ($options['cols'] as $colName => $col) {
                          if (!empty($col['hidden'])) {
                              continue;
                          }
                          echo "<th>";
                          echo $col['col']['label'];
                          if (!empty($col['col']['model'])) {
                              $modelName = $col['col']['model'];
                              $onclick = 'inji.Ui.forms.popUp(\'' . addslashes($modelName) . '\',{},function(elem){'
                                      . 'return function(data,modal){inji.Ui.forms.submitAjax($(elem).closest(\'form\')[0], {notSave: true});}}(this));return false;';
                              echo ' (<a href="" onclick="' . $onclick . ';this.disabled=true;return false;">Создать</a>)';
                          }
                          echo "</th>";
                      }
                      ?>
                        <td>&nbsp;</td>
                    </tr>
                </thead>
                <tbody class="listBody">
                  <?php
                  $i = 0;
                  if (!empty($options['values'])) {
                      foreach ($options['values'] as $row) {
                          echo '<tr>';
                          foreach ($options['cols'] as $colName => $col) {
                              $input = clone $col['input'];
                              if (empty($col['hidden'])) {
                                  echo '<td>';
                              }
                              $input->options['noContainer'] = true;
                              $input->colParams['label'] = false;
                              $input->colParams['value'] = $row[$colName];
                              $input->colName .= '[' . $colName . '][' . ($i) . ']';
                              $input->draw();
                              if (empty($col['hidden'])) {
                                  echo '</td>';
                              }
                          }
                          $i++;
                          echo '<td class="actionTd"><a class="btn btn-danger btn-xs" onclick="inji.Ui.forms.delRowFromList(this);"><i class="glyphicon glyphicon-remove"></i></a></td>';
                          echo '</tr>';
                      }
                  }
                  ?>
                </tbody>
                <tfoot>
                    <tr>
                      <?php
                      foreach ($options['cols'] as $colName => $col) {
                          if (!empty($col['hidden'])) {
                              continue;
                          }
                          echo "<th>{$col['col']['label']}</th>";
                      }
                      ?>
                        <td>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class=" sourceRow" data-counter='<?= $i; ?>'>
            <script>/*
             <tr>
    <?php
    foreach ($options['cols'] as $colName => $col) {
        if (empty($col['hidden'])) {
            echo '<td>';
        }
        $col['input']->options['noContainer'] = true;
        $col['input']->colParams['label'] = false;
        $col['input']->colName.="[{$colName}][]";
        $col['input']->draw();
        if (empty($col['hidden'])) {
            echo '</td>';
        }
    }
    ?>
             <td class="actionTd"><a class="btn btn-danger btn-xs" onclick="inji.Ui.forms.delRowFromList(this);"><i class="glyphicon glyphicon-remove"></i></a></td>
             </tr>
             */</script>
        </div>
        <?php
    }
    ?>
</div>
<?php
//exit();
