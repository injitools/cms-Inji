/**
 * Inji js core
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

function Inji() {
    this.options = {};
    this.onLoadCallbacks = [];
    this.loaded = false;
    this.loadedScripts = {};
}
Inji.prototype.onLoad = function (callback) {
    if (typeof callback == 'function') {
        if (this.loaded) {
            callback();
        }
        else {
            this.onLoadCallbacks.push(callback);
        }
    }
}
Inji.prototype.startCallbacks = function () {
    while (callback = this.onLoadCallbacks.shift()) {
        if (typeof callback == 'function') {
            callback();
        }
    }
    if (this.onLoadCallbacks.length != 0) {
        this.startCallbacks();
    }
    document.getElementById('loading-indicator').style.display = 'none';
    inji.loaded = true;
    console.log('inji start complete');
}
Inji.prototype.start = function (options) {
    console.log('Inji start');
    this.options = options;
    this.loadScripts(options.scripts, 0);
}
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
        }
        else {
            inji.loadedScripts[scripts[key]] = true;
            console.log('js ' + scripts[key] + ' loaded');
        }
        if (typeof (scripts[key + 1]) != 'undefined') {
            inji.loadScripts(scripts, key + 1);
        }
        else {
            console.log('All scripts loaded');
            inji.startCallbacks();
        }
    });
}
Inji.prototype.addScript = function (script, callback) {
    var element = document.createElement('script');
    var src = '';
    if (typeof (script.file) != 'undefined') {
        src = script.file;
    }
    else {
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


}
var inji = new Inji();

