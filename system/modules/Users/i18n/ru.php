<?php
return [
    'needrelogin' => 'Ваша сессия устарела или более недействительна, вам необходимо пройти <a href = "/users/login">авторизацию</a> заново',
    'mailnotfound' => 'Пользователь ${user_mail} не найден, проверьте првильность ввода e-mail или зарегистрируйтесь',
    'logintrylimit' => 'Было совершено более 5ти попыток подбора пароля к вашему аккаунту, для вашей безопасности мы были вынуждены заблокировать к нему доступ.<br />
Для разблокировки аккаунта, воспользуйтесь <a href = "?passre=1&user_mail=${user_mail}">Сбросом пароля</a>',
    'loginfail' => 'Вы ошиблись при наборе пароля или логина, попробуйте ещё раз или воспользуйтесь <a href = "?passre=1&user_mail=${user_mail}">Восстановлением пароля</a>',
    'sucregmsg' => 'Вы были зарегистрированы на сайте ${sitename}<br />для входа используйте ваш почтовый ящик в качестве логина и пароль: ${pass}',
    'repassmailtext' => 'Было запрошено восстановление пароля на сайте ${domain}<br />для продолжения восстановления пароля перейдите по ссылке: <a href = "http://${domain}/?passrecont=1&hash=${hash}">${domain}/?passrecont=1&hash=${hash}</a>',
    'newpassmail' => 'Было запрошено восстановление пароля на сайте ${domain}<br />Ваш новый пароль: ${pass}'
];