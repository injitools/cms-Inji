<?php
$id = 'activeForm-' . \Inji\Tools::randomString();
/**
 * @var \Inji\Ui\ActiveForm $activeForm
 */
$inputs = $activeForm->getInputs();
foreach ($inputs as $inputName => $inputParams) {
    if (is_object($inputParams)) {
        unset($formInputs[$inputName]);
    }
}
?>
<div id='<?= $id; ?>' class="uiActiveForm" data-modelname="<?= $activeForm->modelName; ?>"
     data-formname="<?= $activeForm->formName; ?>" data-inputs='<?= json_encode($formInputs); ?>'>
    <?php
    $form->action = $activeForm->action;
    $form->begin($activeForm->label, ['onsubmit' => $ajax ? 'inji.Ui.forms.submitAjax(this);return false;' : ''], ['activeForm' => $activeForm]);

    if (empty($activeForm->form['noMapCell'])) {
        foreach ($activeForm->map as $row) {
            $colSize = 12 / count($row);
            echo "<div class ='row'>";
            foreach ($row as $col) {
                echo "<div class = 'col-sm-{$colSize}'>";
                if ($col) {
                    $activeForm->drawCol($col, $inputs[$col], $form, $params);
                }
                echo '</div>';
            }
            echo '</div>';
        }
    } else {
        foreach ($activeForm->form['map'] as $row) {
            foreach ($row as $col) {
                if ($col) {
                    $activeForm->drawCol($col, $inputs[$col], $form, $params);
                }
            }
        }
    }
    $form->end($activeForm->model ? ($activeForm->model->pk() ? 'Сохранить' : 'Создать') : 'Отправить', [], ['activeForm' => $activeForm]);
    ?>
</div>
<script>
    inji.onLoad(function () {
        inji.Ui.activeForms.get('#<?= $id; ?>');
    })
</script>