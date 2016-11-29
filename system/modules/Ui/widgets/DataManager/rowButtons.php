<?php

$modelName = $dataManager->modelName;
if ($item) {
    $modelName = get_class($item);
}
$formParams = [
    'dataManagerParams' => $params,
];
$actions = $dataManager->getActions();
$buttons = [];
foreach ($actions as $action => $actionParams) {
    if (class_exists($actionParams['className']) && $actionParams['className']::$rowAction) {
        $buttons[] = $actionParams['className']::rowButton($dataManager, $item, $params, $actionParams);
    }
}
echo implode('&nbsp;', $buttons);
