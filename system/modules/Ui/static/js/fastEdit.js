
var isEditingEnabled;
var widgets = {};

function toggleEditor(btn) {
  if (!isEditingEnabled) {
    $.each($('.fastEdit'), function () {
      if ($(this).closest('a').length > 0) {
        var a = $(this).closest('a');
        a.replaceWith("<div class = 'historyA " + a.attr('class') + "' style = '" + a.attr('style') + "' href = '" + a.attr('href') + "'>" + a.html() + "</div>");
      }
    });
    $.each($('.fastEdit'), function () {
      $(this).attr('contenteditable', true);
      $(this).css('position', 'relative');
      var html = $(this).html();
      var regex = /<!--start:(.*)-->([\s\S]*?)<!--end:\1-->/ig;
      var match;
      while (match = regex.exec(html)) {
        if (match) {
          widgets[match[1]] = match[0];
        }
      }
      $(this).html(html.replace(/<!--start:(.*)-->([\s\S]*?)<!--end:\1-->/ig, '$1'));

      $(this).ckeditor({
        //removePlugins: 'stylescombo',
        //startupFocus: true,
        customConfig: '/static/moduleAsset/libs/libs/ckeditor/inline_config.js'
      });

    });
    isEditingEnabled = true;
    $(btn).text('Отключить редактирование');
  } else {
    $.each($('.fastEdit'), function () {
      $(this).ckeditor().editor.destroy();
      $(this).removeAttr('contenteditable');
      $(this).css('position', 'static');
      //$(this).attr('class', 'fastEdit');
      for (var key in widgets) {
        $(this).html($(this).html().replace(new RegExp(key, "gm"), widgets[key]));
      }
    });
    isEditingEnabled = false;
    $.each($('.fastEdit'), function () {
      if ($(this).closest('.historyA').length > 0) {
        var a = $(this).closest('.historyA');
        a.removeClass('historyA');
        a.replaceWith("<a class = '" + a.attr('class') + "' style = '" + a.attr('style') + "' href = '" + a.attr('href') + "'>" + a.html() + "</a>");
      }
    });
    $(btn).text('Включить редактирование');
  }
  return;
}
$(function () {
  $('body').append("<div class ='btn-group' style = 'position:fixed;right:0;top:0;z-index:100000;' ><button onclick='toggleEditor(this);return false;' class ='btn btn-default btn-xs' >Включить редактирование</button></div>");
})
