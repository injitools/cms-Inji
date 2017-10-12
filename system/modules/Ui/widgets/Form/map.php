<?php
echo empty($options['noContainer']) ? '<div class="form-group">' : '';
echo $label !== false ? "<label>{$label}</label>" : '';
$uid = Tools::randomString();
if (!empty($options['value'])) {
    $options['value'] = json_decode($options['value'], true);
}
?>
    <div id='map<?= $uid; ?>' class="formMap" style="width: 100%; height: 400px"></div>
    <script>
      (function (uid) {
        var myMap;
        var myMapCurPin;
        inji.onLoad(function () {
          ymaps.ready(function () {
            var myPlacemark;
            myMap = new ymaps.Map("map" + uid, {
              // Moscow 55.76 37.64
              // 56.01, 92.85
              center: ["<?= !empty($options['value']['lat']) ? $options['value']['lat'] : '56.01'; ?>", "<?= !empty($options['value']['lng']) ? $options['value']['lng'] : '92.85'; ?>"],
              zoom: 13
            });
              <?php
              if (!empty($options['value'])) {
              ?>
            myMapCurPin = new ymaps.Placemark(["<?= !empty($options['value']['lat']) ? $options['value']['lat'] : '56.01'; ?>", "<?= !empty($options['value']['lng']) ? $options['value']['lng'] : '92.85'; ?>"],
              {iconContent: "<?= !empty($options['value']['address']) ? $options['value']['address'] : implode(',', $options['value']); ?>"},
              {preset: 'islands#greenStretchyIcon'}
            );
            myMap.geoObjects.add(myMapCurPin, 0);
              <?php
              }
              ?>
            myMap.events.add('click', function (e) {
              var myCoords = e.get('coords');
              $('#mapinputs' + uid + ' .lat').val(myCoords[0]);
              $('#mapinputs' + uid + ' .lng').val(myCoords[1]);
              var myGeocoder = ymaps.geocode(myCoords, {kind: 'house'});
              if (myMapCurPin) {
                myMap.geoObjects.remove(myMapCurPin);
              }
              myMapCurPin = new ymaps.Placemark(myCoords,
                {iconContent: 'подождите...'},
                {preset: 'islands#greenStretchyIcon'}
              );
              myMap.geoObjects.add(myMapCurPin, 0);
              myGeocoder.then(
                function (res) {
                  myMap.geoObjects.remove(myMapCurPin);
                  var nearest = res.geoObjects.get(0);
                  $('#mapinputs' + uid + ' .address').val(nearest.properties.get('name'));
                  myMapCurPin = new ymaps.Placemark(myCoords,
                    {iconContent: nearest.properties.get('name')},
                    {preset: 'islands#greenStretchyIcon'}
                  );
                  myMap.geoObjects.add(myMapCurPin, 0);
                },
                function (err) {
                  console.log(err);
                }
              );
            });
          });
        });
      }('<?= $uid; ?>'));
    </script>
    <div id="mapinputs<?= $uid; ?>">
        <input type="hidden" class="lat" name='<?= $name; ?>[lat]'
               value='<?= !empty($options['value']['lat']) ? addcslashes($options['value']['lat'], "'") : ''; ?>'/>
        <input type="hidden" class="lng" name='<?= $name; ?>[lng]'
               value='<?= !empty($options['value']['lng']) ? addcslashes($options['value']['lng'], "'") : ''; ?>'/>
        <input type="hidden" class="address" name='<?= $name; ?>[address]'
               value='<?= !empty($options['value']['address']) ? addcslashes($options['value']['address'], "'") : ''; ?>'/>
    </div>
<?php
echo !empty($options['helpText']) ? "<div class='help-block'>{$options['helpText']}</div>" : '';
echo empty($options['noContainer']) ? '</div>' : '';
?>