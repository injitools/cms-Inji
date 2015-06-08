<?php

$lib = array();
// контроллер - регистрация
// обработка логина при регистрации
$lib['error_user_login'] = 'Не верный формат логина';
$lib['empty_user_login'] = 'Вы не ввели логин';
$lib['strlen_user_login'] = 'Длинна логина не может быть больше 15ти символов и меньше 3х';
$lib['user_login_exists'] = 'Данный логин уже зарегистрирован';
// обработка E-Mail при регистрации
$lib['error_user_mail'] = 'Не верный формат E-Mail';
$lib['is_empty_user_mail'] = 'Вы не ввели E-Mail';
$lib['correct_mail_user_mail'] = 'Не верный формат E-Mail. Пример: mail@mail.ru';
$lib['exists_mail_user_mail'] = 'Данный E-Mail уже зарегистрирован';
// обработка пароля при регистрации
$lib['error_user_pass'] = 'Не верный формат пароля';
$lib['empty_user_pass'] = 'Вы не ввели пароль';
$lib['double_user_pass'] = 'Пароли не совпадают';
// дополнительно
$lib['reg_succes'] = 'Регистрация прошла успешно. Вам на почту было выслано письмо для активации профиля';
// !контроллер - регистрация
// Личный кабинет
$lib['not_autorize'] = 'Вы должны быть зарегистрированы для просмотра своего кабинета';
$lib['user_not_exists'] = 'Пользователь не существует';
$lib['save_succes'] = 'Информация успешно сохранена';
$lib['info_not_update'] = 'Информация не была изменена';
// !Личный кабинет
// форма регистрация
$lib['form_user_mail'] = 'E-Mail';
$lib['form_user_login'] = 'Логин';
$lib['form_user_pass'] = 'Пароль';
$lib['form_double_user_pass'] = 'Повторите пароль';
$lib['form_finish'] = 'Регистрация';
//! форма регистрация
// поля
$lib['field_user_login'] = 'Логин';
$lib['field_user_first_name'] = 'Имя';
$lib['field_user_last_name'] = 'Фамилия';
$lib['field_user_sex'] = 'Пол';
$lib['field_user_sex_1'] = 'Мужской';
$lib['field_user_sex_2'] = 'Женский';
$lib['field_user_photo'] = 'Аватар';
$lib['field_user_firm'] = 'Фирма';
$lib['field_user_addr'] = 'Адрес';
// !поля
return $lib;