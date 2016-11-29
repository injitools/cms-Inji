<tr>
  <?php
  foreach ($row as $html) {
      if (is_array($html)) {
          extract($html);
      }
      echo "<td " . (!empty($class) ? "class='{$class}'" : '') . ">{$html}</td>";
  }
  ?>
</tr>