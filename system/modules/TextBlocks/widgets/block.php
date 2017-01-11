<?php

$code = !empty($param[0]) ? $param[0] : (!empty($code) ? $code : false);
if (!$code) {
    return;
}
$block = \TextBlocks\Block::get($code, 'code');
if (!$block) {
    $block = new TextBlocks\Block(['code' => $code]);
    $block->save();
}
echo \Ui\FastEdit::block($block, 'text');
