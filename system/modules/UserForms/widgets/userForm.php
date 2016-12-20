<?php
if (!empty($params[0])) {
    $form_id = $params[0];
}
if (empty($form_id)) {
    echo('form not found');
    return;
}
$userForm = \UserForms\Form::get((int)$form_id);
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
    $form->input($input->type, 'UserForms[' . (int)$form_id . '][input' . $input->id . ']', $input->label, ['required' => $input->required]);
}
?>
<button class='btn btn-success btn-block'>Отправить</button>
</form>