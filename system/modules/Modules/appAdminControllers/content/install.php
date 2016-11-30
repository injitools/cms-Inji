<h1>Установка модулей</h1>
<form>
  <?php
    $config = Config::app(App::$primary ? App::$primary : App::$cur);
    $modules = array_flip(Module::getInstalled(App::$cur));
    $systemModules = array_slice(scandir(INJI_SYSTEM_DIR . '/modules'), 2);
    foreach ($systemModules as $module) {
        $info = Module::getInfo($module);
        if (!$info || isset($modules[$module]))
            continue;
        ?>
        <div class ="form-group">
            <div class="checkbox">
                <label>
                    <input type ="checkbox" name ="modules[]" value ="<?= $module; ?>" /> <?= $info['name']; ?>
                </label>
            </div>
        </div>
        <?php
    }
    ?>
    <button class="btn btn-primary">Установить</button>
</form>