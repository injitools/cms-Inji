<div class="users">
    <div class="content">
        <div class='row'>
            <div class='box col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1'>
                <h3>Регистрация</h3>
                <?php
                $socials = Users\Social::getList(['where' => ['active', 1]]);
                if ($socials) {
                    ?>
                    <div class="form-group">
                        <label>Регистрация через соц.сети</label><br/>
                        <?php
                        foreach (Users\Social::getList(['where' => ['active', 1]]) as $social) {
                            echo "<a href = '/users/social/auth/{$social->code}'>{$social->name()}</a> ";
                        }
                        ?>
                    </div>
                    <?php
                }
                ?>
                <?php
                $form = new Ui\Form();
                ?>
                <form action='' method='POST'
                      enctype="multipart/form-data" <?= !empty(\App::$primary->users->config['csrf']) ? 'csrf' : ''; ?>>
                    <div class='row'>
                        <div class="col-sm-6">
                            <?php $form->input('email', 'user_mail', 'Электронная почта', ['required' => true]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?php $form->input('text', 'user_name', 'Ваше имя (не фио)'); ?>
                        </div>
                    </div>
                    <div class='row'>
                        <div class="col-sm-6">
                            <?php $form->input('date', 'user_birthday', 'Дата рождения'); ?>
                        </div>
                        <div class="col-sm-6">
                            <?php $form->input('text', 'user_city', 'Город'); ?>
                        </div>
                    </div>
                    <div class='row'>
                        <div class="col-sm-6">
                            <?php
                            if (!empty(App::$cur->users->config['invites'])) {
                                ?>
                                <div class='form-group'>
                                    <label><?= !empty(App::$cur->users->config['invitesName']) ? App::$cur->users->config['invitesName'] : 'Код приглашения'; ?></label>
                                    <input type='text' name='invite_code' class='form-control'
                                           value="<?= (isset($_POST['invite_code']) ? $_POST['invite_code'] : ((!empty($_COOKIE['invite_code']) ? $_COOKIE['invite_code'] : ((!empty($_GET['invite_code']) ? $_GET['invite_code'] : ''))))); ?>"/>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <div class='form-group'>
                        <?php App::$cur->Recaptcha ? App::$cur->Recaptcha->show() : ''; ?>
                    </div>
                    <!--<div class="checkbox">
                        <label>
                            <input type="checkbox" name = 'accept_license' required> Я принимаю <a href = '#userLicense' type="button" data-toggle="modal" data-target="#userLicense">Пользовательское соглашение</a>
                        </label>
                    </div>-->
                    <div class="form-actions text-center">
                        <button class="btn btn-success">Зарегистрироваться</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>