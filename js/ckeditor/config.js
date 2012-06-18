/*
Copyright (c) 2003-2009, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	config.language = 'en';
	config.uiColor = '#B0E0E6';
	
	config.toolbar = 'User';
	config.toolbar_User = [
		['Cut', 'Copy', 'Paste'],
		['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
		['NumberedList','BulletedList'],
		['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
		['Link','Unlink'],
		['Source', '-', 'Maximize'],
		'/',
		['Styles','Format','Font','FontSize'],
		['TextColor','BGColor'],
		['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar']
	];

	config.toolbar = 'Basic';
	config.toolbar_Basic = [
		['Source','Bold','Italic','-','OrderedList','UnorderedList','-','Link','Unlink','FontFormat','FontName',
		'FontSize','TextColor','BGColor','Image','RemoveFormat','-','Smiley','About']
	];

	config.resize_enabled = true;
	config.filebrowserImageUploadUrl = 'ck_upload.php?Type=Image';

	config.extraPlugins = 'charcounter';
};
