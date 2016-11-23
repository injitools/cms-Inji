<?php
echo empty($options['noContainer']) ? '<div class="form-group">' : '';
echo $label !== false ? "<label>{$label}</label>" : '';
$uid = Tools::randomString();
?>
<div id='map<?= $uid; ?>' class="formMap"  style="width: 100%; height: 400px"></div>
<script>
  var myMap<?= $uid; ?>;
  var myMap<?= $uid; ?>CurPin;
  inji.onLoad(function () {
    ymaps.ready(init<?= $uid; ?>);

    function init<?= $uid; ?>() {

      var myPlacemark;
      myMap<?= $uid; ?> = new ymaps.Map("map<?= $uid; ?>", {
        // Moscow 55.76 37.64
        // 56.01, 92.85
        center: ["<?= !empty($options['value']['lat']) ? $options['value']['lat'] : '56.01'; ?>", "<?= !empty($options['value']['lng']) ? $options['value']['lng'] : '92.85'; ?>"],
        zoom: 13
      });
      myMap<?= $uid; ?>.events.add('click', function (e) {
        console.log(e.get('coords'));
        var myCoords = e.get('coords');
        $('[name="<?= $name; ?>[lat]"]').val(myCoords[0]);
        $('[name="<?= $name; ?>[lng]"]').val(myCoords[1]);
        console.log($('[name="<?= $name; ?>[lng]"]').val());
        var myGeocoder = ymaps.geocode(myCoords, {kind: 'house'});
        if (myMap<?= $uid; ?>CurPin) {
          myMap<?= $uid; ?>.geoObjects.remove(myMap<?= $uid; ?>CurPin);
        }
        myMap<?= $uid; ?>CurPin = new ymaps.Placemark(myCoords,
                {iconContent: 'подождите...'},
                {preset: 'islands#greenStretchyIcon'}
        );
        myMap<?= $uid; ?>.geoObjects.add(myMap<?= $uid; ?>CurPin, 0);
        myGeocoder.then(
                function (res) {
                  myMap<?= $uid; ?>.geoObjects.remove(myMap<?= $uid; ?>CurPin);
                  var nearest = res.geoObjects.get(0);
                  $('[name="<?= $name; ?>[address]"]').val(nearest.properties.get('name'));
                  myMap<?= $uid; ?>CurPin = new ymaps.Placemark(myCoords,
                          {iconContent: nearest.properties.get('name')},
                          {preset: 'islands#greenStretchyIcon'}
                  );
                  myMap<?= $uid; ?>.geoObjects.add(myMap<?= $uid; ?>CurPin, 0);
                },
                function (err) {
                  console.log(err);
                }
        );
      });
    }
  });
</script>
<input type ="hidden" name = '<?= $name; ?>[lat]' value = '<?= !empty($options['value']['lat']) ? addcslashes($options['value']['lat'], "'") : ''; ?>' />
<input type ="hidden" name = '<?= $name; ?>[lng]' value = '<?= !empty($options['value']['lng']) ? addcslashes($options['value']['lng'], "'") : ''; ?>' />
<input type ="hidden" name = '<?= $name; ?>[address]' value = '<?= !empty($options['value']['address']) ? addcslashes($options['value']['address'], "'") : ''; ?>' />
<?php
echo!empty($options['helpText']) ? "<div class='help-block'>{$options['helpText']}</div>" : '';
echo empty($options['noContainer']) ? '</div>' : '';
?>