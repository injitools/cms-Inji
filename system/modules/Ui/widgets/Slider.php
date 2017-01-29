<?php
$id = !empty($id) ? $id : 'slider-' . Tools::randomString();
?>
<div id="<?= $id; ?>" class="carousel slide" data-ride="carousel">
  <?php if (empty($noIndicators)) { ?>
        <ol class="carousel-indicators">
          <?php
          $i = 0;
          for ($i = 0; $i < count($slides); $i++) {
              ?>
                <li data-target="#<?= $id; ?>" data-slide-to="<?= $i; ?>" <?= !$i ? 'class="active"' : ''; ?>></li>
                <?php
            }
            ?>

        </ol>
    <?php } ?>
    <!-- Wrapper for slides -->
    <div class="carousel-inner" role="listbox">
      <?php
      $i = 0;
      foreach ($slides as $item) {
          ?>
            <div class="item <?= !$i ? 'active' : ''; ?>">
              <?= !empty($item['href']) ? "<a href = '{$item['href']}' style = 'display:inline-block;'>" : ''; ?>
                <img src="<?= $item['image']; ?>" <?= !empty($item['image']) ? 'alt="' . $item['name'] . '"' : ''; ?> />
                <?= !empty($item['description']) ? "<div class='carousel-caption'>{$item['description']}</div>" : ''; ?>
                <?= !empty($item['href']) ? '</a>' : ''; ?>
            </div>
            <?php
            $i++;
        }
        ?>
    </div>

    <?php if (empty($noArrows)) { ?>
        <!-- Controls -->
        <a class="left carousel-control" href="#<?= $id; ?>" role="button" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="right carousel-control" href="#<?= $id; ?>" role="button" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    <?php } ?>
</div>