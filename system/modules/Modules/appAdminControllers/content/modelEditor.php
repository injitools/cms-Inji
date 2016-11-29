<?php
$form = new Ui\Form();
$form->begin((!empty($modelFullName) ? 'Изменение' : 'Создание') . ' модели');
$cols = [
    'label' => ['type' => 'text', 'label' => 'Название'],
    'code' => ['type' => 'text', 'label' => 'Код'],
    'type' => ['type' => 'select', 'label' => 'Тип', 'options' => [
            'values' => [
                'text' => 'Однострочный текст',
                'textarea' => 'Многострочный текст',
                'number' => 'Целое число',
                'decimal' => 'Число с точкой',
                'dateTime' => 'Дата и время',
                'image' => 'Изображение',
                'currentDateTime' => 'Текущая дата и время (Автоматически заполняется при создании)',
                'relation' => [
                    'text' => 'Зависимость (ссылка на родительский элемент)',
                    'input' => ['type' => 'select', 'options' => ['values' => App::$cur->modules->getSelectListModels(!empty($module) ? $module : false)]]
                ]
            ]
        ]
    ],
];
?>
<div class = "row">
    <div class = "col-md-6">
      <?php $form->input('text', 'name', 'Название', ['placeholder' => 'Например: Статья', 'value' => !empty($modelFullName) ? $modelFullName::$objectName : '']); ?>
    </div>
    <div class = "col-md-6">
        <?php $form->input('text', 'codeName', 'Кодовое обозначение', ['placeholder' => 'Например: Article', 'value' => !empty($modelName) ? $modelName : '', 'helpText' => 'Используйте имена на английском языке. Это обозначение используется для обращения к модели из скрипта']); ?>
    </div>
</div>
<?php
$values = [];
if (!empty($modelFullName)) {
    $relations = $modelFullName::relations();
    foreach ($modelFullName::$cols as $colName => $col) {
        $values[] = [
            'label' => !empty($modelFullName::$labels[$colName]) ? $modelFullName::$labels[$colName] : '',
            'code' => $colName,
            'type' => !empty($col['relation']) ? ['primary' => 'relation', 'aditional' => $relations[$col['relation']]['model']] : $col['type']
        ];
    }
}
$form->input('dynamicList', 'cols', 'Поля', ['cols' => $cols, 'values' => $values]);
$form->end((!empty($modelFullName) ? 'Сохранить' : 'Создать'));
