/**
 * Item name
 *
 * Info
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
/**
 * @class Server
 * @memberOf inji
 */
function Server() {

}

/**
 * @memberOf Server
 */
Server.prototype.init = function () {
  $.each($('form[csrf]'), function () {
    var form = $(this);


    form.submit(function (ev) {
      if (!form.find('[name="csrfToken"]').val()) {
        inji.Server.CSRF(function (key, token) {
          form.append('<input type="hidden" name ="csrfKey" value="' + key + '" />');
          form.append('<input type="hidden" name ="csrfToken" value="' + token + '" />');
          form.submit();
        }, undefined, form.find('button'));
        return false;
      }
    });

  })
};
/**
 * @memberOf Server
 */
Server.prototype.runCommands = function (commands) {
  for (var key in commands) {
    var command = commands[key];
    var callPath = command.call.split('.');
    var curPath = window;
    for (var keyPath in callPath) {
      if (typeof curPath[callPath[keyPath]] == 'undefined') {
        console.log('undefined call path ' + callPath[keyPath] + ' in ' + command.call);
        curPath = null;
        break;
      }
      curPath = curPath[callPath[keyPath]];
    }
    if (curPath !== null) {
      curPath.apply(null, command.params);
    }
  }
};
/**
 * @memberOf Server
 */
Server.prototype.CSRF = function (callback, err, btn) {
  var key = inji.randomString();
  this.request({
    url: '/server/csrf/' + key,
    success: function (token) {
      if (callback !== undefined) {
        callback(key, token);
      }
    },
    error: function (e) {
      if (err !== undefined) {
        err(e);
      }
    }
  }, btn);
};
/**
 * @memberOf Server
 */
Server.prototype.request = function (options, btn) {
  var ajaxOptions = {
    url: '',
    type: 'GET',
    dataType: 'json',
    data: {},
    async: true,
    contentType: false,
    cache: false
  };
  for (var key in options) {
    ajaxOptions[key] = options[key];
  }
  if (options.url && options.url.indexOf('http:') && options.url.indexOf('/') !== 0 && options.url.indexOf('https:') !== 0 && options.url.indexOf(inji.options.appRoot) !== 0) {
    ajaxOptions.url = inji.options.appRoot + (options.url.replace(/^\//g, ''));
  }
  if (btn) {
    if (!$(btn).data('loading-text')) {
      $(btn).data('loading-text', 'подождите');
    }
    btn = $(btn).button().button('loading');
  }
  var callback = null;
  if (options.success !== undefined) {
    callback = options.success;
  }
  ajaxOptions.success = function (data, textStatus, jqXHR) {
    if (btn) {
      btn.button('reset');
    }
    if (ajaxOptions.dataType != 'json') {
      callback(data, textStatus, jqXHR);
    } else {
      if (data.success) {
        if (data.successMsg) {
          noty({text: data.successMsg, type: 'success', timeout: 3500, layout: 'center'});
        }
        if (typeof data.scripts == 'object') {
          inji.loaded = false;
          inji.onLoad(function () {
            inji.Server.runCommands(data.commands);
            if (callback !== null) {
              callback(data.content, textStatus, jqXHR)
            }
          });
          if (data.scripts.length > 0) {
            inji.loadScripts(data.scripts, 0);
          } else {
            inji.startCallbacks();
          }
        } else {
          inji.Server.runCommands(data.commands);
          if (callback !== null) {
            callback(data.content, textStatus, jqXHR);
          }
        }
      } else {
        inji.Server.runCommands(data.commands);
        if (typeof options.error !== 'undefined') {
          options.error(data.error);
        }
        if (data.error) {
          noty({text: data.error, type: 'warning', timeout: 3500, layout: 'center'});
        }
      }
    }
  }
  var errorCallback = null;
  if (typeof options.error !== 'undefined') {
    errorCallback = options.error;
  }
  ajaxOptions.error = function (jqXHR, textStatus, errorThrown) {
    if (typeof btn !== 'undefined') {
      btn.button('reset');
    }
    if (errorCallback != null) {
      errorCallback(jqXHR, textStatus, errorThrown);
    } else if (textStatus !== 'abort') {
      noty({
        text: 'Во время запроса произошла ошибка: ' + textStatus,
        type: 'warning',
        timeout: 3500,
        layout: 'center'
      });
    }
  }
  return $.ajax(ajaxOptions);
};