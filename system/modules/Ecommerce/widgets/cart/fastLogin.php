<?php

$form->input('email', 'user_mail', 'E-Mail', ['value' => (!empty($_POST['user_mail'])) ? $_POST['user_mail'] : (($cart->email) ? $cart->email : '')]);
