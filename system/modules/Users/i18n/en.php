<?php
return [
    'Личный кабинет' => 'Personal Area',
    'Вы вышли из своего профиля' => 'You have been logged out of your profile',
    'Произошла непредвиденная ошибка при авторизации сессии' => 'Unexpected error occured during session authorization',
    'Ваш аккаунт заблокирован' => 'Your account has been suspended',
    'needrelogin' => 'Your session is deprecated or no longer valid, you need to go through <a href = "/users/login"> authorization </a> again',
    'mailnotfound' => 'User ${user_mail} is not found, check the accuracy of the e-mail input or register',
    'logintrylimit' => 'More than 5 attempts were made to find a password for your account, for your safety we had to block access to it. <br />
To unlock your account, please use <a href = "?passre=1&user_mail=${user_mail}"> Reset your password </a>',
    'loginfail' => 'You made a mistake when entering the password or login, try again or use <a href = "?passre=1&user_mail=${user_mail}"> password recovery </a>',
    'Данный почтовый ящик не зарегистрирован в системе' => 'This E-mail is not registered in the system',
    'Введенный вами E-mail зарегистрирован в нашей системе, войдите или введите другой E-mail' => 'The E-mail that you entered is registered in our system, please login or enter another E-mail',
    'Вы ввели не корректный E-mail' => 'You entered an incorrect E-mail',
    'Вы были зарегистрированы. На указанный почтовый ящик был выслан ваш пароль' => 'You have been registered. Your password has been sent to your inbox',
    'Регистрация на сайте ${sitename}' => 'Registration on the site ${sitename}',
    'sucregmsg' => 'You were registered on the site ${sitename} <br /> to log in, use your mailbox as your login and password: ${pass}',
    'На указанный почтовый ящик была выслана инструкция по восстановлению пароля' => 'On the specified mail box the instruction on restoration of the password',
    'Восстановление пароля на сайте ${domain}' => 'Password recovery on the site ${domain}',
    'repassmailtext' => 'You were requested to restore the password on the site ${domain} <br /> to continue password recovery, please go to: <a href="http://${domain}/?passrecont=1&hash=${hash}">${domain}/?passrecont=1&hash=${hash}</a>',
    'Новый пароль на сайте ${domain}' => 'New password on the site ${domain}',
    'newpassmail' => 'Password recovery was requested on the site ${domain} <br /> Your new password: ${pass}',
    'Вы успешно сбросили пароль и были авторизованы на сайте. На ваш почтовый ящик был выслан новый пароль' => 'You have successfully reset the password and have been authorized on the site. A new password has been sent to your mailbox'
];