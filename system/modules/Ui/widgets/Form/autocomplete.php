<?php

$id = Tools::randomString();
echo empty($options['noContainer']) ? '<div class="form-group">' : '';
echo $label !== false ? "<label>{$label}</label>" : '';
$value = !empty($options['value']) ? addcslashes($options['value'], "'") : (!empty($form->userDataTree[$name]) ? addcslashes($form->userDataTree[$name], "'") : '');
$displayValue = '';
if ($value && isset($options['snippet']) && is_string($options['snippet'])) {
    $snippets = App::$cur->Ui->getSnippets('autocomplete');
    if (isset($snippets[$options['snippet']])) {
        $displayValue = $snippets[$options['snippet']]['getValueText']($value, $options['snippetParams']);
    }
}
?>
<input <?= !empty($options['required']) ? 'required' : ''; ?>
        id='<?= $id; ?>'
        type="text"
        autocomplete="off"
        placeholder="<?= !empty($options['placeholder']) ? $options['placeholder'] : ''; ?>"
        class="form-control"
        name='query-<?= $name; ?>'
        value='<?= $displayValue; ?>'
/>

<div class="form-search-cur">Выбрано: <?= $displayValue; ?></div>
<?php
$attributes = [
    'type' => 'hidden',
    'name' => $name,
    'value' => $value
];
if (!empty($options['attributes'])) {
    $attributes = array_merge($attributes, $options['attributes']);
}
echo Html::el('input', $attributes, '', null);
?>

<div class="form-search-results"></div>
<script>
  window.autocomplete<?=$id;?> = function (element, snippet, snippetParams) {
    element.element.onkeyup = function () {
      var inputContainer = element.element.parentNode;
      var selectedDiv = inputContainer.querySelector('.form-search-cur');
      var resultsDiv = inputContainer.querySelector('.form-search-results');
      resultsDiv.innerHTML = '<div class = "text-center"><img src = "' + inji.options.appRoot + 'static/moduleAsset/Ui/images/ajax-loader.gif" /></div>';
      if (this.reqestProcess) {
        this.reqestProcess.abort()
      }
      this.reqestProcess = inji.Server.request({
        url: 'ui/autocomplete',
        data: {
          snippet: snippet,
          snippetParams: snippetParams,
          search: this.value
        },
        success: function (results) {
          resultsDiv.innerHTML = '';
          for (var key in results) {
            var result = results[key];
            var resultElement = document.createElement("div");
            resultElement.setAttribute('objectid', key);
            resultElement.appendChild(document.createTextNode(result));
            resultElement.onclick = function () {
              var value = 0;
              for (var key2 in this.attributes) {
                if (this.attributes[key2].name == 'objectid') {
                  value = this.attributes[key2].value;
                }
              }
              console.log(value);
              inputContainer.querySelector('[type="hidden"]').value = value;
              var element = inputContainer.querySelector('[type="hidden"]');
              if (element.fireEvent !== undefined)
                element.fireEvent("onchange");
              else {
                var evt = document.createEvent("HTMLEvents");
                evt.initEvent("change", false, true);
                element.dispatchEvent(evt);
              }
              inputContainer.querySelector('[type="text"]').value = this.innerHTML;
              selectedDiv.innerHTML = 'Выбрано: ' + this.innerHTML;
              resultsDiv.innerHTML = '';
            };
            resultsDiv.appendChild(resultElement);
          }
          resultsDiv.style.display = 'block';
        }
      })
    };
  };
  inji.onLoad(function () {
    setTimeout(function () {
      new window.autocomplete<?=$id;?>(inji.get('#<?=$id;?>'), '<?=$options['snippet'];?>', <?=json_encode($options['snippetParams']);?>);
    },100);
  });
</script>
<?php
echo !empty($options['helpText']) ? "<div class='help-block'>{$options['helpText']}</div>" : '';
echo empty($options['noContainer']) ? '</div>' : '';
?>
