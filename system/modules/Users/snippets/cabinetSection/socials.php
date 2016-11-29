<?php

if (!Users\Social::getList(['where' => ['active', 1]])) {
    return false;
}
return [
    'name' => 'Социальные сети',
    'fullWidget' => 'Users\cabinet/socials'
];
