/**
 * Main Ui object
 *
 * @returns {Ui}
 */
inji.Ui = new function () {
  inji.onLoad(function () {
    inji.Ui.bindMenu($('.nav-list-categorys'));
    inji.Ui.modals = new Modals();
    inji.Ui.forms = new Forms();
    inji.Ui.editors = new Editors();
    inji.Ui.autocomplete = new Autocomplete();

  });
  this.customSelect = new CustomSelect();
  this.bindMenu = function (container) {
    container.find('.nav-left-ml').toggle();
    container.find('label.nav-toggle span').click(function () {
      $(this).parent().parent().children('ul.nav-left-ml').toggle(300);
      var cs = $(this).attr("class");
      if (cs == 'nav-toggle-icon glyphicon glyphicon-chevron-right') {
        $(this).removeClass('glyphicon-chevron-right').addClass('glyphicon-chevron-down');
      }
      if (cs == 'nav-toggle-icon glyphicon glyphicon-chevron-down') {
        $(this).removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-right');
      }
    });
  };
  this.requestInfo = function (options, callback) {
    var id = 'resultForm' + inji.randomString();
    var body = '<form id ="' + id + '">';
    body += '<h2>' + options.header + '</h2>';
    for (var key in options.inputs) {
      body += '<div class = "form-group">';
      body += '<label>' + options.inputs[key].label + '</label>';
      body += '<input type = "' + options.inputs[key].type + '" name = "' + key + '" class ="form-control" />';
      body += '</div>';
    }
    body += '<button class = "btn btn-primary">' + options.btn + '</button>';
    body += '</form>';
    var modal = inji.Ui.modals.show('', body);
    $('#' + id).on('submit', function () {
      callback($('#' + id).serializeArray());
      modal.modal('hide');
      return false;
    });
  }
};

function CustomSelect() {
  this.bind = function (jqueryEl) {
    var self = this;
    jqueryEl.each(function () {
      new self.fn($(this));
    });
  };
  this.fn = function (jqueryEl) {
    jqueryEl[0].__inji_customSelect__ = this;
    var self = this;
    this.select = jqueryEl.find('select');
    var htmlOptions = this.select.html();
    htmlOptions = htmlOptions.replace(/option/g, 'div');
    console.log(htmlOptions);
    this.custom = $('<div class="customSelect">\
                                        <div class="main-box">\
                                            <div class="current-item"></div>\
                                            <div class="chevron"><div><i class="glyphicon glyphicon-chevron-down"></i></div><div style="display:none;"><i class="glyphicon glyphicon-chevron-up"></i></div></div>\
                                        </div>\
                                        <div class="item-list">\
                                        ' + htmlOptions + '\
                                        </div>\
                                    </div>');

    this.custom.find('.item-list>*').addClass('list-item');
    this.custom.find('.main-box').click(function (e) {
      var element = $(this).next()[0];
      $('.customSelect .item-list').each(function () {
        if (element != this && $(this).css('display') == 'block') {
          $(this).slideUp();
          $(this).closest('.customSelect').find('.chevron>*').slideToggle();
        }
      });
      self.toggle();
    });
    jqueryEl.prepend(this.custom);
    var text = this.select.find("option:selected").html();
    this.custom.find('.current-item').html(text);
    this.select.change(function () {
      var text = self.select.find("option:selected").html();
      self.custom.find('.current-item').html(text);
    });
    this.custom.find('.list-item').click(function () {
      self.select.find('option:selected')[0].selected = false;
      self.select.find('option').get($(this).index()).selected = true;
      self.select.change();
      self.toggle();
    });
    if (this.select.find(':selected').val() != 0) {
      $(this.custom.find('.list-item')[$(this).find(':selected').val()]).click();
      $(this).change();
    }
    this.toggle = function () {
      console.log('toggle');
      self.custom.find('.item-list').slideToggle();
      self.custom.find('.chevron>*').slideToggle();
    };

  };
  $('body').off('click.closeCustomSelect');
  $('body').on('click.closeCustomSelect', function (e) {
    if ($(e.target).closest('.customSelect').length == 0) {
      $('.customSelect .item-list').each(function () {
        if ($(this).css('display') == 'block') {
          $(this).slideUp();
          $(this).closest('.customSelect').find('.chevron>*').slideToggle();
        }
      });
    }
  });
}

function Autocomplete() {
  this.autocompletes = [];
  this.bind = function (element, options, params) {
    var autocomplete = new this.fn(element, options, params);
    element.element.__inji_autocomplete = autocomplete;
    this.autocompletes.push(autocomplete);
  };
  this.fn = function (element, snippet, snippetParams) {
    this.element = element;
    this.snippet = snippet;
    this.snippetParams = snippetParams;
    this.reqestProcess = null;
    this.inputContainer = element.element.parentNode;
    this.selectedDiv = this.inputContainer.querySelector('.form-search-cur');
    this.resultsDiv = this.inputContainer.querySelector('.form-search-results');
    this.changer = this.inputContainer.querySelector('.custominput-clear');
    this.hidden = this.inputContainer.querySelector('[type="hidden"]');

    var self = this;
    this.element.element.onkeyup = function () {
      self.loadResult(this.value);
    };

    this.clear = function () {
      this.setValue('', '')
    };
    this.setValue = function (value, text) {
      this.hidden.value = value;
      if (this.hidden.fireEvent !== undefined)
        this.hidden.fireEvent("onchange");
      else {
        var evt = document.createEvent("HTMLEvents");
        evt.initEvent("change", false, true);
        this.hidden.dispatchEvent(evt);
      }
      this.inputContainer.querySelector('[type="text"]').value = text;
      this.selectedDiv.innerHTML = 'Выбрано: ' + text;
      this.resultsDiv.style.display = 'none';
      this.changer.style.display = value ? 'block' : 'none';
    };
    this.loadResult = function (search) {
      if (this.reqestProcess) {
        this.reqestProcess.abort()
      }
      this.resultsDiv.innerHTML = '<div class = "text-center"><img src = "' + inji.options.appRoot + 'static/moduleAsset/Ui/images/ajax-loader.gif" /></div>';
      this.resultsDiv.style.display = 'block';
      this.reqestProcess = inji.Server.request({
        url: 'ui/autocomplete',
        data: {
          snippet: this.snippet,
          snippetParams: this.snippetParams,
          search: search
        },
        success: function (results) {
          self.resultsDiv.innerHTML = '';
          for (var key in results) {
            var result = results[key];
            var resultElement = document.createElement("div");
            resultElement.setAttribute('objectid', key);
            resultElement.appendChild(document.createTextNode(result));
            resultElement.onclick = function () {
              var value = 0;
              for (var key2 in this.attributes) {
                if (this.attributes[key2].name === 'objectid') {
                  value = this.attributes[key2].value;
                }
              }
              self.setValue(value, this.innerText);
            };
            self.resultsDiv.appendChild(resultElement);
          }
          self.resultsDiv.style.display = 'block';
        }
      });
    };

  };
}

/**
 * Editors
 *
 */
var Editors = function () {
  this.ckeditor = false;
  this.checkEditors();
  inji.on('loadScript', function () {
    inji.Ui.editors.checkEditors();
  });
  inji.onLoad(function () {
    inji.Ui.editors.loadIn('.htmleditor');
  })
};
Editors.prototype.checkEditors = function () {
  if (!this.ckeditor && typeof CKEDITOR != 'undefined') {
    this.ckeditor = true;
  }
};
Editors.prototype.loadAll = function () {

};
Editors.prototype.loadIn = function (selector, search) {
  if (this.ckeditor) {
    setTimeout(function () {
      var instances;
      if (typeof search != 'undefined') {
        instances = $(selector).find(search);
      } else {
        instances = $(selector);
      }
      $.each(instances, function () {
        var editor;
        var _this = this;
        if ($(this).closest('.modal').length == 0 || $(this).closest('.modal').hasClass('in')) {
          editor = $(_this).ckeditor({customConfig: inji.options.appRoot + 'static/moduleAsset/libs/libs/ckeditor/program/userConfig.php'});
        }
        if ($(this).closest('.modal').length != 0) {
          $(this).closest('.modal').on('shown.bs.modal', function () {
            setTimeout(function () {
              editor = $(_this).ckeditor({customConfig: inji.options.appRoot + 'static/moduleAsset/libs/libs/ckeditor/program/userConfig.php'});
            }, 1000);
          });
          $(this).closest('.modal').on('hide.bs.modal', function () {
            if (editor.editor) {
              editor.editor.updateElement();
              editor.editor.destroy();
              delete editor.editor;
              $(this).closest('.modal').unbind('hide.bs.modal');
              $(this).closest('.modal').unbind('shown.bs.modal');
            }

          })
        }
      })
    }, 1000);
  }
};
Editors.prototype.beforeSubmit = function (form) {
  if (this.ckeditor) {
    $.each(CKEDITOR.instances, function () {
      this.updateElement();
    });
    $.each($(form).find('.cke'), function () {
      var instance = $(this).attr('id').replace('cke_', '');
      $(CKEDITOR.instances[instance].element).closest('.modal').unbind();
      CKEDITOR.instances[instance].destroy();
    });
  }
};
/**
 * Modals objects
 *
 * @returns {Modals}
 */
var Modals = function () {
  this.modals = 0;
};
Modals.prototype.show = function (title, body, code, size) {
  if (code == null) {
    code = 'modal' + (++this.modals);
  }
  if ($('#' + code).length == 0) {
    if (size == null) {
      size = '';
    }
    if (title) {
      title = '<div class="modal-header">\
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>\
                  <h4 class="modal-title">' + title + '</h4>\
                </div>';
    } else {
      title = '';
    }
    var html = '\
          <div class="modal fade" id = "' + code + '" >\
            <div class="modal-dialog ' + size + '">\
              <div class="modal-content">\
                ' + title + '\
                <div class="modal-body">\
                ' + body + '\
                </div>\
                <div class="modal-footer">\
                  <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>\
                </div>\
              </div>\
            </div>\
          </div>';
    $('body').append(html);

  }
  var modal = $('#' + code);
  $('body').append(modal);
  modal.modal('show');
  return modal;
};

/**
 * Forms object
 *
 * @returns {Forms}
 */
function Forms() {
  this.dataManagers = 0;
  this.formCallbacks = {};
}

Forms.prototype.popUp = function (item, params, callback) {
  var code = item;

  if (typeof params == 'undefined') {
    params = {};
  }
  if (typeof (params.relation) != 'undefined') {
    code += params.relation;
  }
  code = code.replace(/:/g, '_').replace(/\\/g, '_');
  var modal = inji.Ui.modals.show('', '<div class = "text-center"><img src = "' + inji.options.appRoot + 'static/moduleAsset/Ui/images/ajax-loader.gif" /></div>', code, 'modal-lg');
  inji.Server.request({
    url: 'ui/formPopUp/',
    data: {item: item, params: params},
    success: function (data) {
      modal.find('.modal-body').html(data);
      if (callback) {
        inji.Ui.forms.formCallbacks[modal.find('.form').attr('id')] = callback;
      }
      inji.Ui.editors.loadIn(modal.find('.modal-body'), '.htmleditor');
    }
  });
};
Forms.prototype.submitAjax = function (form, params) {
  inji.Ui.editors.beforeSubmit(form);
  var form = $(form);
  var container = form.parent().parent();
  var btn = form.find('button');
  btn.text('Подождите');
  btn[0].disabled = true;
  btn.data('loading-text', "Подождите");

  var url = form.attr('action');
  if (params) {
    var first = true;
    if (url.indexOf('?') >= 0) {
      first = false;
    }
    for (var key in params) {
      url += (first ? '?' : '&') + key + '=' + params[key];
    }
  }
  var formData = new FormData(form[0]);
  inji.Server.request({
    url: url,
    type: 'POST',
    data: formData,
    processData: false,
    success: function (data) {
      if (inji.Ui.forms.formCallbacks[form.attr('id')]) {
        inji.Ui.forms.formCallbacks[form.attr('id')]();
        delete inji.Ui.forms.formCallbacks[form.attr('id')];
      }
      container.html(data);
      inji.Ui.editors.loadIn(container, '.htmleditor');
      inji.Ui.dataManagers.reloadAll();
      if (params && !params.notSave) {
        var btn = container.find('form button');
        var text = btn.text();
        btn.text('Изменения сохранены!');
        setTimeout(function () {
          btn.text(text)
        }, 3000);
      }
    }
  });
};
Forms.prototype.addRowToList = function (btn) {
  var container = $(btn).closest('.dynamicList');
  var counter = parseInt(container.find('.sourceRow').data('counter')) + 1;
  container.find('.sourceRow').data('counter', counter);
  var trHtml = container.find('.sourceRow script').html().replace(/^\/\*/g, '').replace(/\*\/$/g, '').replace(/\[counterPlaceholder\]/g, '[' + counter + ']');
  container.find('.listBody').append(trHtml);
};
Forms.prototype.checkAditionals = function (select) {
  var selectedInputAd = $(select).find('option:selected').attr('data-aditionalInput');
  var nextSelect = $(select).next();
  var i = 0;
  if ($(select).data('aditionalEnabled') == 1) {
    $(select).data('aditionalEnabled', 0);
  }
  while (nextSelect.length) {
    if (i != selectedInputAd) {
      nextSelect[0].disabled = true;
      nextSelect.addClass('hidden');
    } else {
      if ($(select).data('aditionalEnabled') != 1) {
        $(select).data('aditionalEnabled', 1);
      }
      nextSelect[0].disabled = false;
      nextSelect.removeClass('hidden');
    }
    nextSelect = $(nextSelect).next();
    i++;
  }
};
Forms.prototype.delRowFromList = function (btn) {
  $(btn).closest('tr').remove();
};

inji.Ui.activeForms = new function () {
  this.activeForms = [];
  this.get = function (selector) {
    var element = inji.get(selector);
    if (element && element.data('activeFormIndex') !== null) {
      return this.activeForms[element.data('activeFormIndex')];
    }
    this.initial(element);
  };
  this.initial = function (element) {
    var activeForm = new ActiveForm();
    this.activeForms.push(activeForm);

    activeForm.index = this.activeForms.length - 1;
    activeForm.element = element;
    activeForm.modelName = element.data('modelname');
    activeForm.formName = element.data('formname');
    activeForm.inputs = element.data('inputs');

    element.element.setAttribute('activeFormIndex', activeForm.index);

    activeForm.load();
  }
};

function ActiveForm() {
  this.modelName;
  this.formName;
  this.reqestProcess;
  this.inputs = {};
  this.index;
  this.element;
  this.load = function () {
    for (var inputName in this.inputs) {
      var inputParams = this.inputs[inputName];
      var self = this;
      if (this.inputHandlers[inputParams.type]) {
        var query = '#' + this.element.element.id + ' [name="query-ActiveForm_' + this.formName + '[' + this.modelName.replace(/\\/g, '\\\\') + '][' + inputName + ']"]';
        this.inputHandlers[inputParams.type](inji.get(query), inputName, this);
      }
      if (inputParams.onChange == 'reloadForm') {
        var query = '#' + this.element.element.id + ' [name="ActiveForm_' + this.formName + '[' + this.modelName.replace(/\\/g, '\\\\') + '][' + inputName + ']"]';
        $(query).on('change', function () {
          inji.Ui.forms.submitAjax($('#' + self.element.element.id + ' form')[0], {notSave: true});
        })
      }
    }
  };
  this.inputHandlers = {
    search: function (element, inputName, activeForm) {
      element.element.onkeyup = function () {
        var inputContainer = element.element.parentNode;
        var selectedDiv = inputContainer.querySelector('.form-search-cur');
        var resultsDiv = inputContainer.querySelector('.form-search-results');
        resultsDiv.innerHTML = '<div class = "text-center"><img src = "' + inji.options.appRoot + 'static/moduleAsset/Ui/images/ajax-loader.gif" /></div>';
        if (this.reqestProcess) {
          this.reqestProcess.abort()
        }
        this.reqestProcess = inji.Server.request({
          url: 'ui/activeForm/search',
          data: {
            modelName: activeForm.modelName,
            formName: activeForm.formName,
            inputName: inputName,
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
                for (key in this.attributes) {
                  if (this.attributes[key].name == 'objectid') {
                    value = this.attributes[key].value;
                  }
                }
                inputContainer.querySelector('[type="hidden"]').value = value;
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
    }
  };
}