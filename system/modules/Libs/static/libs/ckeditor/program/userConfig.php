/**
* @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
* For licensing, see LICENSE.md or http://ckeditor.com/license
*/
CKEDITOR.plugins.addExternal('inlinesave', '/static/moduleAsset/libs/libs/ckeditor/plugins/inlinesave/');
CKEDITOR.plugins.addExternal('injiwidgets', '/static/moduleAsset/libs/libs/ckeditor/plugins/injiwidgets/');
CKEDITOR.plugins.addExternal('font', '/static/moduleAsset/libs/libs/ckeditor/plugins/font/');
CKEDITOR.plugins.addExternal('justify', '/static/moduleAsset/libs/libs/ckeditor/plugins/justify/');
CKEDITOR.dtd.$removeEmpty['i'] = false;
CKEDITOR.editorConfig = function( config ) {
config.filebrowserBrowseUrl = '/admin/files/managerForEditor?folder=images';
config.filebrowserImageBrowseUrl = '/admin/files/managerForEditor';
config.contentsCss = ['/view/editorcss'];
config.allowedContent = true;
config.height = '300px';
config.extraPlugins = 'injiwidgets,justify,colorbutton,font';
config.pasteFilter = 'plain-text';
config.allowedContent = true;
config.extraAllowedContent = '*(*);*{*}';

<?php
if (!empty(App::$cur->libs->config['libConfig']['ckeditor']['pasteFromWordRemoveStyle'])) {
    echo 'config.pasteFromWordRemoveStyle = true;';
}
?>
};
<?php
if (!empty(App::$cur->libs->config['libConfig']['ckeditor']['pasteFromWordRemoveStyle'])) {
    echo 'CKEDITOR.config.pasteFromWordRemoveStyle = true;';
}
?>
CKEDITOR.basePath = '/cache/static/bowerLibs/ckeditor/';
CKEDITOR.plugins.basePath = CKEDITOR.basePath + 'plugins/';
