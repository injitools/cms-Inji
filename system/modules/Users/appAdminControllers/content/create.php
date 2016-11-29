<form action = '' method = 'POST' >
    <div class="row">
        <div class="col-lg-12">
            <div class="box dark">
                <header>
                    <div class="icons">
                        <i class="icon-cogs"></i>
                    </div>
                    <h5>Основные параметры</h5>
                    <!-- .toolbar -->
                    <div class="toolbar">
                        <div class="btn-group">
                            <a href="#div-1" data-toggle="collapse" class="btn btn-default btn-sm accordion-toggle minimize-box">
                                <i class="icon-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <!-- /.toolbar -->
                </header>
                <div id="div-1" class="accordion-body collapse in body form-horizontal">

                    <div class="form-group">
                        <label for="text1" class="control-label col-lg-2">Логин</label>
                        <div class="col-lg-10">
                            <input type="text" id="text1" placeholder="" class="form-control" name = 'user_login' value = ''>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="text1" class="control-label col-lg-2">Имя</label>
                        <div class="col-lg-10">
                            <input type="text" id="text1" placeholder="" class="form-control" name = 'user_name' value = ''>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="text1" class="control-label col-lg-2">E-mail</label>
                        <div class="col-lg-10">
                            <input type="text" id="text1" placeholder="" class="form-control" name = 'user_mail' value = ''>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-2">Роль</label>
                        <div class="col-lg-10">
                            <select class="form-control" name = 'user_role_id'>
                              <?php
                              foreach ($roles as $role) {
                                  echo "<option value = '{$role['role_id']}'>{$role['role_name']}</option>";
                              }
                              ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="text1" class="control-label col-lg-2"> пароль</label>
                        <div class="col-lg-10">
                            <input type="password"  class="form-control" name = 'user_pass[]' >
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="text1" class="control-label col-lg-2">повторите пароль</label>
                        <div class="col-lg-10">
                            <input type="password"  class="form-control" name = 'user_pass[]'>
                        </div>
                    </div>
                    <input type = 'submit' class = 'btn btn-primary' value = 'Сохранить' />
                </div>

            </div>
        </div>
    </div>
</form>
