<div class="users">
  <div class="content">
    <div class='row'>
      <div class = 'box col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1'>
        <h3>Вход</h3>
        <div class="form-group">
          <?php
          $socials = Users\Social::getList(['where' => ['active', 1]]);
          if ($socials) {
            echo 'Войти через: ';
            foreach (Users\Social::getList(['where' => ['active', 1]]) as $social) {
              $text = $social->image ? '<img src ="' . Statics::file($social->image->path) . '">' : $social->name();
              echo "<a href = '/users/social/auth/{$social->code}'>{$text}</a> ";
            }
          }
          ?>
        </div>
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
          <input type ='hidden' name ='autorization' value ='1' />
          <div class="form-actions text-center">
            <button class ="btn btn-primary" >Войти</button> <a href ="/users/registration" class ="btn btn-primary" >Зарегистрироваться</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>