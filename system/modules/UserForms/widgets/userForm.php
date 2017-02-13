<?php
$formId = !empty($formId) ? $formId : (!empty($params[0]) ? $params[0] : 0);
if (empty($formId)) {
    echo('form not found');
    return;
}
$btnText = !empty($btnText) ? $btnText : (!empty($params[1]) ? $params[1] : 'Отправить');
$userForm = \UserForms\Form::get((int) $formId);
if (!$userForm) {
    echo('form not found');
    return;
}
$form = new Ui\Form();
$form->begin();
?>
<?php
if ($userForm->description) {
    echo "<p class = 'text-center'>{$userForm->description}</p>";
}
foreach ($userForm->inputs(['order' => ['weight']]) as $input) {
    $form->input($input->type, 'UserForms[' . (int) $formId . '][input' . $input->id . ']', $input->label, ['required' => $input->required]);
}
?>
<button class='btn btn-success btn-block'><?= $btnText; ?></button>
</form>