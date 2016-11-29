<?php

$html = '';
foreach ($buttons as $button) {
    if (!empty($button['class'])) {
        $button['class'] = 'btn btn-primary btn-sm ' . $button['class'];
    } else {
        $button['class'] = 'btn btn-primary btn-sm';
    }
    $html .= Html::el('a', $button, $button['text']);
}
echo $html;
