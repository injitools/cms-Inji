/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function (config) {
    config.filebrowserBrowseUrl = '/admin/static/filemanager/browser/default/browser.html?Connector=/admin/filemanager';
    config.filebrowserImageBrowseUrl = '/admin/static/filemanager/browser/default/browser.html?Connector=/admin/filemanager';
    config.contentsCss = ['/view/editorcss'];
    //config.stylesSet = 'template_styles:/templates/current/css/editor.styles.js';
    config.allowedContent = true;
// Toolbar configuration generated automatically by the editor based on config.toolbarGroups.
    config.toolbar = [
        {name: 'document', groups: ['mode', 'document', 'doctools'], items: ['Source', '-', 'Inlinesave', 'Preview', 'Print']},
        {name: 'clipboard', groups: ['clipboard', 'undo'], items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo']},
        {name: 'editing', groups: ['find', 'selection', 'spellchecker'], items: ['Find', 'Replace', '-', 'SelectAll']},
        {name: 'links', items: ['Link', 'Unlink', 'Anchor']},
        {name: 'insert', items: ['Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'Iframe']},
        '/',
        {name: 'basicstyles', groups: ['basicstyles', 'cleanup'], items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat']},
        {name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi'], items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language']},
        '/',
        {name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize']},
        {name: 'colors', items: ['TextColor', 'BGColor']},
        {name: 'tools', items: ['Maximize', 'ShowBlocks']},
        {name: 'inji', items: ['InjiWidgets']},
    ];

// Toolbar groups configuration.
    config.toolbarGroups = [
        {name: 'document', groups: ['mode', 'document', 'doctools']},
        {name: 'clipboard', groups: ['clipboard', 'undo']},
        {name: 'editing', groups: ['find', 'selection']},
        {name: 'links'},
        {name: 'insert'},
        '/',
        {name: 'basicstyles', groups: ['basicstyles', 'cleanup']},
        {name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi']},
        '/',
        {name: 'styles'},
        {name: 'colors'},
        {name: 'tools'},
        {name: 'inji'},
    ];
    config.extraPlugins = 'inlinesave,injiwidgets';
};
