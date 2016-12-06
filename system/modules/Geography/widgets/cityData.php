<?php

echo Geography\City\Data::get([['code', $params[0]], ['city_id', Geography\City::$cur->id]])->data;
