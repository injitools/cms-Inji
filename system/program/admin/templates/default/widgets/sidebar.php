<div id="sidebar-wrapper">
  <ul class="sidebar-nav">
    <li class="sidebar-brand">
      <a href="/">
        Вернуться на сайт
      </a>
    </li>
    <?php
    if (class_exists('Users\User')) {
      App::$cur->ui;
      ?>
      <hr />
      <div class="row userWidget">
        <div class="col-xs-4">
          <img src="<?= Users\User::$cur->info->photo ? Users\User::$cur->info->photo->path : '/static/system/images/no-image.png'; ?>" class="img-responsive" />
        </div>
        <div class="col-xs-8">
          <?= Users\User::$cur->name(); ?><br />
          <?= Users\User::$cur->mail; ?>
        </div>

        <div class = "col-xs-12">
          <a href = "#" onclick = "inji.Ui.forms.popUp('Users\\User:<?= Users\User::$cur->id; ?>');return false;">Редактировать</a> |
          <a href = "?logout">Выйти</a>
        </div>
      </div>
      <hr />
      <li>
        <a href = "/admin">Панель управления</a>
      </li>
      <?php
      $where[] = ['group_id', Users\User::$cur->group_id];
      if (Users\User::$cur->group_id == 3) {
        $where[] = ['group_id', 0, '=', 'OR'];
      }
      $menu = Menu\Menu::get([['code', 'sidebarMenu'], $where]);
      foreach ($menu->items(['where' => ['parent_id', 0], 'order' => ['weight', 'asc']]) as $item) {
        echo "<li><a href = '{$item->href}'>{$item->name}</a>";
        $childItems = Menu\Item::getList(['where' => ['parent_id', $item->id]]);
        if ($childItems) {
          echo "<ul>";
          foreach ($childItems as $item) {
            echo "<li><a href = '{$item->href}'>{$item->name}</a>";
          }
          echo "</ul>";
        }
        echo "</li>";
      }
    }
    ?>
    <li>
      <a href = "?logout">Выйти</a>
    </li>
  </ul>
</div>