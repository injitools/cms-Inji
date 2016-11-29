<form action = '' method = 'POST' >
    <div class="row">
        <div class="col-lg-6">
            <div class="box dark">
                <header>
                    <div class="icons">
                        <i class="fa fa-cogs"></i>
                    </div>
                    <h5>Основные параметры</h5>
                    <!-- .toolbar -->
                    <div class="toolbar">
                        <div class="btn-group">
                            <a href="#div-1" data-toggle="collapse" class="btn btn-default btn-sm accordion-toggle minimize-box">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <!-- /.toolbar -->
                </header>
                <div id="div-1" class="accordion-body collapse in body form-horizontal">
                    <div class="form-group">
                        <label for="text1" class="control-label col-lg-2">Логин</label>
                        <div class="col-lg-10">
                            <input type="text" id="text1" placeholder="" class="form-control" name = 'user_login' value = '<?php echo $user->user_login; ?>'>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="text1" class="control-label col-lg-2">Имя</label>
                        <div class="col-lg-10">
                            <input type="text" id="text1" placeholder="" class="form-control" name = 'user_name' value = '<?php echo $user->user_name; ?>'>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="text1" class="control-label col-lg-2">E-mail</label>
                        <div class="col-lg-10">
                            <input type="text" id="text1" placeholder="" class="form-control" name = 'user_mail' value = '<?php echo $user->user_mail; ?>'>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-2">Роль</label>
                        <div class="col-lg-10">
                            <select class="form-control" name = 'user_role_id'>
                              <?php
                              foreach ($roles as $role) {
                                  if ($role['role_id'] == $user->user_role_id)
                                      $selected = 'selected = "selected"';
                                  else
                                      $selected = '';
                                  echo "<option {$selected} value = '{$role['role_id']}'>{$role['role_name']}</option>";
                              }
                              ?>
                            </select>
                        </div>
                    </div>
                    <input type = 'submit' class = 'btn btn-primary' value = 'Сохранить' />
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="box dark">
                <header>
                    <div class="icons">
                        <i class="fa fa-cogs"></i>
                    </div>
                    <h5>Смена пароля</h5>
                    <!-- .toolbar -->
                    <div class="toolbar">
                        <div class="btn-group">
                            <a href="#div-1" data-toggle="collapse" class="btn btn-default btn-sm accordion-toggle minimize-box">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <!-- /.toolbar -->
                </header>
                <div id="div-1" class="accordion-body collapse in body form-horizontal">
                    <div class ='col-xs-12'>
                        <div class="form-group">
                            <label for="text1" class="control-label"> Введите для смены пароля</label>

                            <input type="password"  class="form-control" name = 'user_pass[]' >

                        </div>
                        <div class="form-group">
                            <label for="text1" class="control-label">повторите пароль</label>

                            <input type="password"  class="form-control" name = 'user_pass[]'>

                        </div>

                    </div>
                    <div class ='clearfix'></div>
                    <input type = 'submit' class = 'btn btn-primary' value = 'Сохранить' />

                </div>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <header>
                    <div class="icons">
                        <i class="fa fa-edit"></i>
                    </div>
                    <h5>Заметка администратора</h5>
                    <!-- .toolbar -->
                    <div class="toolbar">
                        <ul class="nav">
                            <li>
                                <a class="minimize-box" data-toggle="collapse" href="#div-3">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </li>
                        </ul>
                    </div><!-- /.toolbar -->
                </header>
                <div id="div-3" class="body collapse in">
                    <div class ='form-group'>
                        <textarea class='ckeditor' id = 'material_text' name = 'user_admin_text' style = 'width:100%;height:60em;'rows = 50><?php echo $user->user_admin_text; ?></textarea>

                    </div>
                    <input type = 'submit' class = 'btn btn-primary' value = 'Сохранить' />

                </div>
            </div>
        </div><!-- /.col-lg-12 -->
    </div>

    <script type="text/javascript" src="/admin/static/ckeditor/ckeditor.js"></script>
</form>