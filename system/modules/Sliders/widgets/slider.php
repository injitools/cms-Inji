<?php
if (!empty($params[0])) {
    $sliderId = $params[0];
}
if (!empty($sliderId)) {
    $slider = \Sliders\Slider::get($sliderId);
}
if (!empty($alias)) {
    $slider = Sliders\Slider::get($alias, 'alias');
}
$slides = $slider->slides(['order' => ['weight', 'ASC']]);
?>
<div id="sliderWidget-<?= $slider->id; ?>" class="carousel slide" data-ride="carousel">
    <!-- Indicators -->
    <ol class="carousel-indicators">
      <?php
      $i = 0;
      for ($i = 0; $i < count($slides); $i++) {
          ?>
            <li data-target="#sliderWidget-<?= $slider->id; ?>" data-slide-to="<?= $i; ?>" <?= !$i ? 'class="active"' : ''; ?>></li>
            <?php
        }
        ?>

    </ol>

    <!-- Wrapper for slides -->
    <div class="carousel-inner" role="listbox">
      <?php
      $i = 0;
      foreach ($slides as $item) {
          ?>
            <div class="item <?= !$i ? 'active' : ''; ?>">
              <?php
              if ($item->link) {
                  echo "<a href = '{$item->link}' style = 'display:inline-block;'>";
              }
              ?>
                <img src="<?= Statics::file($item->image->path); ?>" alt="<?= $item->name; ?>">
                <div class="carousel-caption">
                  <?= $item->description; ?>
                </div>
                <?php
                if ($item->link) {
                    echo "</a>";
                }
                ?>
            </div>
            <?php
            $i++;
        }
        ?>
    </div>

    <?php
    if (!isset($arrows) || $arrows) {
        ?>
        <!-- Controls -->
        <a class="left carousel-control" href="#sliderWidget-<?= $slider->id; ?>" role="button" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="right carousel-control" href="#sliderWidget-<?= $slider->id; ?>" role="button" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
        <?php
    }
    ?>
</div>