<?php

return function ($step = null, $params = []) {
    $options = ['max_height' => 1200, 'max_width' => 1200];
    $types = [
        [
            'dir' => '/static/mediafiles/images/',
            'ext' => 'png',
            'group' => 'image',
            'allow_resize' => 1,
            'options' => json_encode($options)
        ],
        [
            'dir' => '/static/mediafiles/images/',
            'ext' => 'jpeg',
            'group' => 'image',
            'allow_resize' => 1,
            'options' => json_encode($options)
        ],
        [
            'dir' => '/static/mediafiles/images/',
            'ext' => 'jpg',
            'group' => 'image',
            'allow_resize' => 1,
            'options' => json_encode($options)
        ],
        [
            'dir' => '/static/mediafiles/images/',
            'ext' => 'gif',
            'group' => 'image',
            'allow_resize' => 1,
            'options' => json_encode($options)
        ],
        [
            'dir' => '/static/mediafiles/docs/',
            'ext' => 'xls',
            'group' => 'doc',
            'allow_resize' => 0,
            'options' => ''
        ],
        [
            'dir' => '/static/mediafiles/docs/',
            'ext' => 'pdf',
            'group' => 'doc',
            'allow_resize' => 0,
            'options' => ''
        ],
        [
            'dir' => '/static/mediafiles/docs/',
            'ext' => 'docx',
            'group' => 'doc',
            'allow_resize' => 0,
            'options' => ''
        ],
        [
            'dir' => '/static/mediafiles/docs/',
            'ext' => 'doc',
            'group' => 'doc',
            'allow_resize' => 0,
            'options' => ''
        ],
        [
            'dir' => '/static/mediafiles/docs/',
            'ext' => 'xlsx',
            'group' => 'doc',
            'allow_resize' => 0,
            'options' => ''
        ],
    ];
    foreach ($types as $type) {
        $typeObject = new \Files\Type($type);
        $typeObject->save();
    }
};