<?php
if (class_exists($options['relation']['model'])) {
    if ($options['input']->activeForm->model && $options['input']->activeForm->model->pk()) {
        $dataManager = new \Ui\DataManager($options['relation']['type'] == 'relModel' ? $options['relation']['relModel'] : $options['relation']['model'], 'manager');
        $params = ['relation' => $options['input']->colParams['relation']];
        if (!empty($options['input']->activeFormParams['appType'])) {
            $params['appType'] = $options['input']->activeFormParams['appType'];
        }
        $dataManager->draw($params, $options['input']->activeForm->model);
        ?>
        <script>
            inji.onLoad(function () {
              inji.Ui.dataManagers.get($('#<?= $dataManager->managerId; ?>'));
            })
        </script>
        <?php
    } else {
        $dataManager = new \Ui\DataManager($options['relation']['type'] == 'relModel' ? $options['relation']['relModel'] : $options['relation']['model'], 'manager');
        $dataManager->predraw();
        echo '<h3>' . $dataManager->table->name . '</h3>';
        echo '<h4 class=" text-muted">Чтобы добавить связи, сначала создайте объект</h4>';
        echo '<p class=" text-muted">Просто заполните доступные поля и нажмите кнопку внизу формы. После этого дополнительные поля разблокируются</p>';
    }
} else {
    echo "<!-- class{$options['relation']['model']} none exist -->";
}
?>