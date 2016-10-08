/**
 * Data Manager objects
 * 
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
inji.Ui.dataManagers = {
  instances: {},
  get: function (element) {
    if ($(element).hasClass('dataManager')) {
      if (typeof (this.instances[$(element).attr('id')]) != 'undefined') {
        return this.instances[$(element).attr('id')];
      } else {
        return this.instances[$(element).attr('id')] = new DataManager($(element));
      }
    } else {
      if ($(element).closest('.dataManager').length == 1 && typeof (this.instances[$(element).closest('.dataManager').attr('id')]) != 'undefined') {
        return this.instances[$(element).closest('.dataManager').attr('id')];
      } else if ($(element).closest('.dataManager').length == 1) {
        return this.instances[$(element).closest('.dataManager').attr('id')] = new DataManager($(element).closest('.dataManager'));
      }
    }
    return null
  },
  popUp: function (item, params) {
    var code = item;

    if (typeof (params.relation) != 'undefined') {
      code += params.relation;
    }
    code = code.replace(/\:/g, '_').replace(/\\/g, '_');
    var modal = inji.Ui.modals.show('', '<div class = "text-center"><img src = "' + inji.options.appRoot + 'static/moduleAsset/Ui/images/ajax-loader.gif" /></div>', code, 'modal-lg');
    inji.Server.request({
      url: 'ui/dataManager/',
      dataType: 'json',
      data: {item: item, params: params},
      success: function (data) {
        modal.find('.modal-body').html(data);
        $.each(modal.find('.modal-body .dataManager'), function () {
          inji.Ui.dataManagers.instances[$(this).attr('id')] = new DataManager($(this));
        });
      }
    });
  },
  reloadAll: function () {
    for (var key in this.instances) {
      this.instances[key].reload();
    }
  }
}



function DataManager(element) {
  this.element = element;
  this.params = element.data('params');
  this.filters = this.params.filters ? this.params.filters : {};
  this.modelName = element.data('modelname');
  this.managerName = element.data('managername');
  this.options = element.data('options');

  this.categoryModel = this.options.categorys && this.options.categorys.model ? this.options.categorys.model : '';
  this.categoryPath = '/';
  this.categoryId = 0;

  this.limit = 30;
  this.page = 1;
  this.sortered = {};
  this.categoryIndex = '';
  this.mode = '';
  this.all = 0;
  this.ajaxUrl = 'ui/dataManager/loadRows';
  if (this.options.ajaxUrl) {
    this.ajaxUrl = this.options.ajaxUrl;
  }
  var instance = this;
  $(this.element).find('thead [type="checkbox"],tfoot [type="checkbox"]').click(function () {
    var index = $(this).closest('th').index();
    if (!this.checked) {
      $(instance.element).find('.datamanagertable tbody tr').each(function () {
        $($(this).find('td').get(index)).find('[type="checkbox"]')[0].checked = false;
      });
    } else {
      $(instance.element).find('.datamanagertable tbody tr').each(function () {
        $($(this).find('td').get(index)).find('[type="checkbox"]')[0].checked = true;
      });
    }
  });
  if (this.options.sortMode) {
    $(this.element).find('.modeBtn').on('click', function () {
      if (instance.mode != $(this).data('mode')) {
        instance.mode = $(this).data('mode');
        instance.all = 1;
        instance.page = 1;
        instance.load();
      } else {
        instance.mode = '';
        instance.all = 0;
        instance.page = 1;
        instance.load();
      }
    });
  }
  $(this.element).find('.pagesContainer').on('click', 'a', function () {
    instance.page = $(this).attr('href').match(/page\=(\d+)\&?/)[1];
    instance.limit = $(this).attr('href').match(/limit\=(\d+)\&?/)[1];
    instance.load();
    return false;
  });
  self = this;
  $(document).on('scroll', function () {
    self.flowPanel();
  });
  $(window).on('resize', function () {
    self.flowPanel();
  });
  self.flowPanel();

  if (window.location.hash && window.location.hash != '#') {
    var windowsHash = JSON.parse(decodeURIComponent(window.location.hash.substr(1)));
    if (windowsHash[this.modelName + ':' + this.managerName]) {
      var hashData = windowsHash[this.modelName + ':' + this.managerName];
      this.limit = hashData.params.limit;
      this.page = hashData.params.page;
      this.sortered = hashData.sortered;
      this.all = hashData.all;
      this.filters = hashData.filters;
      if (hashData.filters && this.element.find('.dataManagerFilters [name^="datamanagerFilters"]').length > 0) {
        this.element.find('.dataManagerFilters [name^="datamanagerFilters"]').each(function () {
          var maths = $(this).attr('name').match(/\[([^\]]+)\]/g);
          for (key in maths) {
            maths[key] = maths[key].replace(/([\[\]])/g, '');
          }
          if (hashData.filters[maths[0]]) {
            if (typeof maths[2] != 'undefined') {
              $(this).val(hashData.filters[maths[0]][maths[1]][maths[2]]);
            } else {
              if ($(this).attr('type') == 'checkbox') {
                $(this)[0].checked = hashData.filters[maths[0]][maths[1]];
              } else {
                $(this).val(hashData.filters[maths[0]][maths[1]]);
              }
            }
          }
        });
      }


      // var data = {params: params, modelName: this.modelName, managerName: this.managerName, filters: filters, sortered: this.sortered, mode: this.mode, all: this.all};
    }
  }

  this.load();
}

DataManager.prototype.newItem = function (model, options) {
  if (this.categoryId) {
    if (!options.preset) {
      options.preset = {};
    }
    options.preset['category_id'] = this.categoryId;
  }
  inji.Ui.forms.popUp(model, options);
}
DataManager.prototype.newCategory = function () {
  var options = {preset: {}};
  options.preset['parent_id'] = this.categoryId;
  inji.Ui.forms.popUp(this.categoryModel, options);
}
DataManager.prototype.delRow = function (key) {
  if (confirm('Вы уверены, что хотите удалить элемент?'))
  {
    inji.Server.request({
      url: 'ui/dataManager/delRow',
      data: {params: this.params, modelName: this.modelName, key: key, managerName: this.managerName},
      success: function () {
        inji.Ui.dataManagers.reloadAll();
      }
    });
  }
}
DataManager.prototype.delCategory = function (key) {
  if (confirm('Вы уверены, что хотите удалить элемент?'))
  {
    inji.Server.request({
      url: 'ui/dataManager/delCategory',
      data: {params: this.params, modelName: this.modelName, key: key, managerName: this.managerName},
      success: function () {
        inji.Ui.dataManagers.reloadAll();
      }
    });
  }
}
DataManager.prototype.reload = function () {
  this.load();
}
DataManager.prototype.load = function (options) {
  if ($('#' + this.element.attr('id')).length == 0) {
    delete inji.Ui.dataManagers[this.element.attr('id')];
    return;
  }
  var dataManager = this;
  if (typeof this.params == 'string') {
    var params = JSON.parse(this.params);
  }
  if (Object.prototype.toString.call(this.params) === '[object Array]') {
    var params = {};
  } else {
    var params = this.params;
  }
  params.limit = this.limit;
  params.page = this.page;
  params.categoryPath = this.categoryPath;
  filters = this.filters;
  if (this.element.find('.dataManagerFilters [name^="datamanagerFilters"]').length > 0) {
    this.element.find('.dataManagerFilters [name^="datamanagerFilters"]').each(function () {
      var maths = $(this).attr('name').match(/\[([^\]]+)\]/g);
      for (key in maths) {
        maths[key] = maths[key].replace(/([\[\]])/g, '');
      }
      if (!filters[maths[0]]) {
        filters[maths[0]] = {};
      }
      if (typeof maths[2] != 'undefined') {
        if (!filters[maths[0]][maths[1]]) {
          filters[maths[0]][maths[1]] = {};
        }
        filters[maths[0]][maths[1]][maths[2]] = $(this).val();
      } else {
        if ($(this).attr('type') == 'checkbox' && !$(this)[0].checked) {
          filters[maths[0]][maths[1]] = 0;
        } else {
          filters[maths[0]][maths[1]] = $(this).val();
        }
      }
    });
  }
  if (this.options.sortable) {
    sortableIndexes = [];
    var i = 0;
    for (key2 in  this.options.cols) {
      var colname;
      if (typeof this.options.cols[key2] == 'object') {
        colname = key2;
      } else {
        colname = this.options.cols[key2];
      }
      for (key in this.options.sortable) {
        if (colname == this.options.sortable[key]) {
          sortableIndexes.push(parseInt(i));
        }
      }
      i++;
    }
    for (key in sortableIndexes) {
      var shift = 1;

      shift++;

      var headTh = $(dataManager.element.find('thead th').get(sortableIndexes[key] + shift));
      var footTh = $(dataManager.element.find('tfoot th').get(sortableIndexes[key] + shift));
      if (!headTh.hasClass('sortable')) {
        headTh.html('<a href = "#">' + headTh.html() + '</a>');
        headTh.addClass('sortable');
        if (this.options.preSort && this.options.preSort[this.options.sortable[key]]) {
          if (this.options.preSort[this.options.sortable[key]] == 'asc') {
            headTh.addClass('sorted-asc');
            this.sortered[sortableIndexes[key]] = 'asc';
          } else if (this.options.preSort[this.options.sortable[key]] == 'desc') {
            headTh.addClass('sorted-desc');
            this.sortered[sortableIndexes[key]] = 'desc';
          }
        }
        //sorted-desc
        headTh.click(function () {
          $(this).addClass('clickedsort');
          $('.sortable').not('.clickedsort').removeClass('sorted-asc').removeClass('sorted-desc');
          $(this).removeClass('clickedsort');
          dataManager.sortered = {};
          if (!$(this).hasClass('sorted-desc') && !$(this).hasClass('sorted-asc')) {
            $(this).addClass('sorted-desc');
            dataManager.sortered[$(this).index() - shift] = 'desc';
            dataManager.reload();
          } else if ($(this).hasClass('sorted-desc')) {
            $(this).removeClass('sorted-desc');
            $(this).addClass('sorted-asc');
            dataManager.sortered[$(this).index() - shift] = 'asc';
            dataManager.reload();
          } else if ($(this).hasClass('sorted-asc')) {
            $(this).removeClass('sorted-asc');
            delete dataManager.sortered[$(this).index() - shift];
            dataManager.reload();
          }
          return false;
        })
      }
      if (!footTh.hasClass('sortable')) {
        footTh.html('<a href = "#">' + footTh.html() + '</a>');
        footTh.addClass('sortable');
        footTh.click(function () {
          $(this).addClass('clickedsort');
          $('.sortable').not('.clickedsort').removeClass('sorted-asc').removeClass('sorted-desc');
          $(this).removeClass('clickedsort');
          dataManager.sortered = {};
          if (!$(this).hasClass('sorted-desc') && !$(this).hasClass('sorted-asc')) {
            $(this).addClass('sorted-desc');
            dataManager.sortered[$(this).index() - shift] = 'desc';
            dataManager.reload();
          } else if ($(this).hasClass('sorted-desc')) {
            $(this).removeClass('sorted-desc');
            $(this).addClass('sorted-asc');
            dataManager.sortered[$(this).index() - shift] = 'asc';
            dataManager.reload();
          } else if ($(this).hasClass('sorted-asc')) {
            $(this).removeClass('sorted-asc');
            delete dataManager.sortered[$(this).index() - shift];
            dataManager.reload();
          }
          return false;
        })
      }
    }
  }
  var data = {params: params, modelName: this.modelName, managerName: this.managerName, filters: filters, sortered: this.sortered, mode: this.mode, all: this.all};
  if (options && options.download) {
    data.download = true;
    var url = this.ajaxUrl;
    if (url.indexOf(inji.options.appRoot) !== 0) {
      url = inji.options.appRoot + url;
    }
    window.location = url + '?' + $.param(data);
    return;
  }
  dataManager.element.find('.datamanagertable tbody').html('<tr><td colspan="' + dataManager.element.find('thead tr th').length + '"><div class = "text-center"><img src = "' + inji.options.appRoot + 'static/moduleAsset/Ui/images/ajax-loader.gif" /></div></td></tr>');
  var instance = this;


  var windowHash = {};
  if (window.location.hash && window.location.hash != '#') {
    windowHash = JSON.parse(decodeURIComponent(window.location.hash.substr(1)));
  }
  windowHash[data.modelName + ':' + data.managerName] = data;
  window.location.hash = JSON.stringify(windowHash);
  inji.Server.request({
    url: this.ajaxUrl,
    data: data,
    success: function (data) {
      dataManager.element.find('.datamanagertable tbody').html(data.rows);
      dataManager.element.find('.pagesContainer').html(data.pages);

      if (dataManager.options.options && dataManager.options.options.formOnPage) {
        $('.' + dataManager.modelName.replace(/\\/g, '_') + '_' + dataManager.managerName + '_create_btn').each(function () {
          var createBtn = $(this);
          var btnHref = createBtn.attr('href');
          btnHref = btnHref.substr(0, btnHref.indexOf('redirectUrl='));
          btnHref += 'redirectUrl=' + window.location.pathname;
          createBtn.attr('href', btnHref + '&dataManagerHash=' + window.location.hash.substr(1));
        });
      }

      //dataManager.flowPages();
      if (dataManager.options.sortMode) {
        if (dataManager.mode != 'sort') {
          dataManager.element.find('.modeBtn').removeClass('active');
        } else {
          dataManager.element.find('.modeBtn').addClass('active');
        }
      }
      $(instance.element).find('.datamanagertable tbody').sortable().sortable("disable");
      if (dataManager.mode == 'sort') {
        $(instance.element).find('.datamanagertable tbody').sortable({
          stop: function (event, ui) {
            ids = $(instance.element).find('.datamanagertable tbody tr');
            i = 0;
            while (ids[i]) {
              var key = $(ids[i++]).find('td').get(1).innerHTML;
              inji.Server.request({
                url: 'ui/dataManager/updateRow',
                data: {params: instance.params, modelName: instance.modelName, key: key, col: 'weight', col_value: i, managerName: instance.managerName, silence: true},
              });
            }
          }
        }).sortable("enable");
      }
    }
  });
  if (dataManager.element.find('.categoryTree').length > 0) {
    dataManager.element.find('.categoryTree').html('<img class ="img-responsive" src = "' + inji.options.appRoot + 'static/moduleAsset/Ui/images/ajax-loader.gif" />');
    inji.Server.request({
      url: 'ui/dataManager/loadCategorys',
      data: {params: params, modelName: this.modelName, managerName: this.managerName},
      success: function (data) {
        dataManager.element.find('.categoryTree').html(data);
        dataManager.element.find('.categoryTree [data-path="' + instance.categoryPath + '"]').parent().addClass('active');
        dataManager.element.find('.treeview').treeview();
        $(instance.element).find('.categoryTree').sortable().sortable("disable");
        if (dataManager.mode == 'sort') {
          $(instance.element).find('.categoryTree ul a[data-path]').map(function () {
            this.onclick = null
          });
          $(instance.element).find('.categoryTree ul').sortable({
            stop: function (event, ui) {
              ids = $(instance.element).find('li');
              console.log(instance.element,ids)
              i = 0;
              while (ids[i]) {
                var key = $(ids[i]).find('>a').data('id');
                var model = $(ids[i]).find('>a').data('model');
                console.log(key , model)
                if (key && model) {
                  inji.Server.request({
                    url: 'ui/dataManager/updateRow',
                    data: {params: instance.params, modelName: model, key: key, col: 'weight', col_value: i, managerName: instance.managerName, silence: true},
                  });
                }
                i++;
              }
            }
          }).sortable("enable");
        }
      }
    });
  }
}
DataManager.prototype.switchCategory = function (categoryBtn) {
  this.categoryPath = $(categoryBtn).data('path');
  this.categoryId = $(categoryBtn).data('id');
  this.categoryIndex = $(categoryBtn).data('index');
  this.reload();
}
DataManager.prototype.flowPanel = function () {

  var elHeight = $(this.element).offset().top + $(this.element).height();
  var scrollHeight = $(document).scrollTop() + $(window).height();
  if (elHeight > scrollHeight && scrollHeight < scrollHeight + 37) {
    $(this.element).find('.dataManager-bottomFloat').css('right', $(window).width() - ($(this.element).offset().left + $(this.element).width()) + 'px');
    $(this.element).find('.dataManager-bottomFloat').css('position', 'fixed');
  } else {
    $(this.element).find('.dataManager-bottomFloat').css('right', 'auto');
    $(this.element).find('.dataManager-bottomFloat').css('position', 'relative');
  }
}
DataManager.prototype.groupAction = function (actionName) {
  var ids = '';
  var rows = {};
  $(this.element).find('.datamanagertable tbody tr').each(function () {
    if ($($(this).find('td').get(0)).find('[type="checkbox"]')[0].checked) {
      ids += ',' + $($(this).find('td').get(0)).find('[type="checkbox"]').val();
      rows[$($(this).find('td').get(0)).find('[type="checkbox"]').val()] = $(this);
    }
  });
  if (ids != '') {
    var action = this.options.actions[actionName];
    if (action.customJsChecker) {
      if (!window[action.customJsChecker](this, rows)) {
        return;
      }
    }
    if (action.aditionalInfo) {
      var id = inji.randomString();
      var html = '<form id ="' + id + '"><h3>Для этой груповой операции требуется дополнительная информация</h3>';
      for (key in action.aditionalInfo) {
        var input = action.aditionalInfo[key];
        html += '<div class = "form-group"><label>' + input.label + '</label><input type="' + input.type + '" name ="' + key + '" class = "form-control" value = "" /></div>';
      }
      html += '<div class = "form-group"><button class="btn btn-primary" >' + action.name + '</button></div></form>';
      inji.Ui.modals.show('Дополнительная информация', html, 'modal' + id);
      var instance = this;
      $('#' + id).submit(function () {
        $(this).closest('.modal').modal('hide');
        var adInfo = {};
        if ($(this).find('input').length > 0) {
          $.each($(this).find('input'), function () {
            adInfo[$(this).attr('name')] = $(this).val();
          });
        }
        inji.Server.request({
          url: 'ui/dataManager/groupAction',
          data: {params: instance.params, modelName: instance.modelName, ids: ids, managerName: instance.managerName, action: actionName, adInfo: adInfo},
          success: function () {
            inji.Ui.dataManagers.reloadAll();
          }
        });
        return false;
      });
    } else {
      inji.Server.request({
        url: 'ui/dataManager/groupAction',
        data: {params: this.params, modelName: this.modelName, ids: ids, managerName: this.managerName, action: actionName},
        success: function () {
          inji.Ui.dataManagers.reloadAll();
        }
      });
    }
  }
}
DataManager.prototype.rowSelection = function (type) {
  $(this.element).find('.datamanagertable tbody tr').each(function () {
    if ($($(this).find('td').get(0)).find('[type="checkbox"]')[0].checked && (type == 'unSelectAll' || type == 'inverse')) {
      $($(this).find('td').get(0)).find('[type="checkbox"]')[0].checked = false;
    } else if (!$($(this).find('td').get(0)).find('[type="checkbox"]')[0].checked && (type == 'selectAll' || type == 'inverse')) {
      $($(this).find('td').get(0)).find('[type="checkbox"]')[0].checked = true;
    }
  });
}

inji.onLoad(function () {
  $.each($('.dataManager'), function () {
    inji.Ui.dataManagers.instances[$(this).attr('id')] = new DataManager($(this));
  });
});