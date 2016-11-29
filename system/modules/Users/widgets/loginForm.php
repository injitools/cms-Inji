<?php
if (!Users\User::$cur->id) {
    ?>
    <div class='row'>
        <div class = 'box col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1'>
            <h3>Вход</h3>
            <form action = '' method = 'POST' >
                <div class ='row'>
                    <div class="col-sm-6">
                        <div class ='form-group'>
                            <label>Логин или email</label>
                            <input type ='text' name ='user_login' class ='form-control' placeholder ='mail@mail.ru' required />
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class ='form-group'>
                            <label>Пароль</label>
                            <input type ='password' name ='user_pass' class ='form-control' placeholder ='Пароль' required />
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <a href ="/users/registration">Зарегистрироваться</a>
                    <?php
                    foreach (Users\Social::getList(['where' => ['active', 1]]) as $social) {
                        echo "<a href = '/users/social/auth/{$social->code}'>{$social->name()}</a> ";
                    }
                    ?>
                </div>
                <input type ='hidden' name ='autorization' value ='1' />
                <div class="form-actions text-center">
                    <button class ="btn btn-success" >Войти</button>
                </div>
            </form>
        </div>
    </div>
    <?php
}