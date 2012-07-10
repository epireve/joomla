/*
Copyright (c) 2010,Jomsocial Team . All rights reserved.
*/

CKEDITOR.editorConfig = function(config)
{
    config.contentsLanguage = 'en';
    config.resize_enabled = false;
    config.toolbar = 'JomSocial';
    config.skin = 'jomsocial';
    config.removePlugins = 'elementspath,save,font';
    config.toolbarCanCollapse = false;
    config.uiColor = '#EEEEEE';
    config.language = 'en';

    config.toolbar_JomSocial =
    [
    [
    'Bold', 'Italic', 'Underline', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'Indent', 'Outdent', 'NumberedList', 'BulletedList',
    '-','Image', 'Link', 'Unlink','-', 'Source'
    ]
    ]

};
