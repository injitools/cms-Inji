      
<div class="text-center">
    <h3><?php echo Users\User::$cur->name(); ?></h3>
    <img src="<?= Statics::file(Users\User::$cur->info->photo ? Users\User::$cur->info->photo->path : '/static/system/images/no-image.png', '200x200'); ?>" />
    <br />
    <a href ="/admin" class = 'btn btn-primary'>Перейти в админ панель</a>
    <a href ="/admin?logout=1" class = 'btn btn-danger'>Выйти</a>

</div>
