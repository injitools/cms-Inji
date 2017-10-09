/**
 * Inji js core
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

function Inji() {
  this.options = {
    appRoot: ''
  };
  this.onLoadCallbacks = [];
  this.loaded = false;
  this.listeners = {};
  this.loadedScripts = {};
}

Inji.prototype.onLoad = function (callback, start) {
  if (typeof callback === 'function') {
    if (this.loaded) {
      callback();
    } else {
      if (start) {
        this.onLoadCallbacks.unshift(callback);
      } else {
        this.onLoadCallbacks.push(callback);
      }
    }
  }
};
Inji.prototype.startCallbacks = function () {
  console.log('inji start onload');
  var callback;
  while (callback = this.onLoadCallbacks.shift()) {
    if (typeof callback == 'function') {
      callback();
    }
  }
  if (this.onLoadCallbacks.length != 0) {
    this.startCallbacks();
  }
  var indicator = document.getElementById('loading-indicator');
  if (indicator) {
    indicator.style.display = 'none';
  }
  inji.loaded = true;
  console.log('inji start complete');
};
Inji.prototype.start = function (options) {
  console.log('Inji start');
  for (var key in options.compresedScripts) {
    inji.loadedScripts[options.compresedScripts[key]] = true;
  }
  this.options = options;
  if (options.onLoadModules) {
    this.onLoad(function () {
      for (var key in options.onLoadModules) {
        if (typeof inji[key] == 'undefined') {
          inji[key] = new window[key]();
          if (typeof (inji[key].init) == 'function') {
            console.log(key + ' init');
            inji[key].init();
          }
        }
      }
    }, true)
  }
  if (options.scripts.length > 0) {
    this.loadScripts(options.scripts, 0);
  } else {
    inji.startCallbacks();
  }
};
Inji.prototype.loadScripts = function (scripts, key) {
  this.addScript(scripts[key], function () {
    if (typeof (scripts[key].name) != 'undefined') {
      inji.loadedScripts[scripts[key].file] = true;
      if (typeof inji[scripts[key].name] == 'undefined') {
        console.log('js ' + scripts[key].name + '(' + scripts[key].file + ') loaded');
        inji[scripts[key].name] = new window[scripts[key].name]();
        if (typeof (inji[scripts[key].name].init) == 'function') {
          inji[scripts[key].name].init();
        }
      }
    } else if (typeof (scripts[key].file) != 'undefined') {
      inji.loadedScripts[scripts[key].file] = true;
      console.log('js ' + scripts[key].file + ' loaded');

    } else {
      inji.loadedScripts[scripts[key]] = true;
      console.log('js ' + scripts[key] + ' loaded');
      inji.event('loadScript', scripts[key]);
    }
    if (typeof (scripts[key + 1]) != 'undefined') {
      inji.loadScripts(scripts, key + 1);
    } else {
      console.log('All scripts loaded');
      inji.startCallbacks();
    }
  });
};
Inji.prototype.addScript = function (script, callback) {
  var element = document.createElement('script');
  var src = '';
  if (typeof (script.file) != 'undefined') {
    src = script.file;
  } else {
    src = script;
  }
  if (inji.loadedScripts[src]) {
    if (typeof (callback) == 'function') {
      callback();
    }
    return true;
  }
  element.src = src;
  element.type = 'text/javascript';
  if (typeof (callback) == 'function') {
    element.onload = callback;
  }
  document.head.appendChild(element);


};
Inji.prototype.on = function (eventType, callback) {
  if (typeof this.listeners[eventType] == 'undefined') {
    this.listeners[eventType] = [];
  }
  this.listeners[eventType].push(callback);
}
Inji.prototype.event = function (eventType, object) {
  if (typeof this.listeners[eventType] != 'undefined') {
    for (var key in this.listeners[eventType]) {
      this.listeners[eventType][key](eventType, object);
    }
  }
};
Inji.prototype.randomString = function (length) {
  if (!length) {
    length = 20;
  }
  var text = "";
  var chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

  for (var i = 0; i < length; i++)
    text += chars.charAt(Math.floor(Math.random() * chars.length));

  return text;
};
Inji.prototype.numberFormat = function (number, decimals, dec_point, thousands_sep) {
  //// Format a number with grouped thousands
  // 
  // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +	 bugfix by: Michael White (http://crestidg.com)

  var i, j, kw, kd, km;

  // input sanitation & defaults
  if (isNaN(decimals = Math.abs(decimals))) {
    decimals = 2;
  }
  if (dec_point == undefined) {
    dec_point = ",";
  }
  if (thousands_sep == undefined) {
    thousands_sep = ".";
  }

  i = parseInt(number = (+number || 0).toFixed(decimals)) + "";

  if ((j = i.length) > 3) {
    j = j % 3;
  } else {
    j = 0;
  }

  km = (j ? i.substr(0, j) + thousands_sep : "");
  kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep);
  //kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).slice(2) : "");
  kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).replace(/-/, 0).slice(2) : "");


  return km + kw + kd;
};
Inji.prototype.get = function (query) {
  var element = document.querySelector(query);
  if (element) {
    return new function () {
      this.element = element;
      this.attr = function (name) {
        for (var key in this.element.attributes) {
          var attr = element.attributes[key];
          if (attr.name == name) {
            return attr.value;
          }
        }
        return null;
      };
      this.data = function (name) {
        var data = this.attr('data-' + name);
        try {
          return JSON.parse(data);
        } catch (e) {
          return data;
        }

      }
    };
  }
};
Inji.prototype.parseQueryString = function (string) {
  var result = {};

  string = string.indexOf('?') === 0 ? string.substr(1) : string;
  if (!string.length) {
    return {};
  }
  string = decodeURIComponent(string.replace(/\[[\"\']/g, '[').replace(/[\"\']\]/g, ']'));
  var result = {};
  var params = string.split('&');
  for (var key in params) {
    var item = params[key];
    var itemPaths = item.match(/\[([^\]]*)\]/g);
    if (itemPaths === null) {
      result[item.split('=')[0]] = item.split('=')[1];
      continue;
    }
    var rootParam = item.substr(0, item.indexOf('['));
    if (typeof result[rootParam] == 'undefined') {
      result[rootParam] = {};
    }
    var resultCurPath = result[rootParam];
    for (var itemPathKey in itemPaths) {
      var itemPath = itemPaths[itemPathKey].replace('[', '').replace(']', '');
      if (itemPath == '') {
        itemPath = Object.keys(resultCurPath).length;
      }
      if (typeof resultCurPath[itemPath] == 'undefined') {
        resultCurPath[itemPath] = parseInt(itemPathKey) + 1 < itemPaths.length ? {} : item.split('=')[1];
      }
      if (parseInt(itemPathKey) + 1 < itemPaths.length) {
        resultCurPath = resultCurPath[itemPath];
      }
    }

  }
  return result;
};
var inji = new Inji();