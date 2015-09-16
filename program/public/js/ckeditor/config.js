/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/
 
CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	
	config.uiColor = '#ddd';
	config.toolbar = 'Rich';

	config.toolbar_Rich =
	[
		['Source','-','Format','Styles','FontSize'],
		['Outdent','Indent','Blockquote'],
		['TextColor','BGColor'],
		['Link','Unlink','Anchor'],
		['Templates','Preview'],
		['ShowBlocks','Maximize','Insertcode'],
		'/',
		['RemoveFormat','Undo','Redo','-','Bold','Italic','Underline','Strike'],
		['Subscript','Superscript','NumberedList','BulletedList'],
		['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
		['PageBreak','Table','HorizontalRule','Smiley','SpecialChar','-','wpMore', 'Image','Insertcode']
	];
	
	config.toolbar_Poor =
		[
			['Source'],
			['RemoveFormat','Undo','Redo','-','Bold','Italic','Underline','Strike','Insertcode'],
			['Outdent','Indent','Blockquote'],
			['TextColor','BGColor'],
			['Link','Unlink','Anchor'],
			['Subscript','Superscript','NumberedList','BulletedList'],
		];

	config.filebrowserUploadUrl = '/js/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
	config.fontSize_sizes = '10/10px;12/12px;14/14px;16/16px;18/18px;20/20px;22/22px;24/24px;28/28px;32/32px;48/48px;';
	 config.extraPlugins = 'insertcode'; 
};

// 让编辑器保持干净代码格式
CKEDITOR.on( 'instanceReady', function( ev ){
    with (ev.editor.dataProcessor.writer) { 
	 	  setRules("p", {indent : false, breakAfterOpen : false, breakBeforeClose : false} ); 
	 	  setRules("h1", {indent : false, breakAfterOpen : false, breakBeforeClose : false} ); 
	 	  setRules("h2", {indent : false, breakAfterOpen : false, breakBeforeClose : false} ); 
	 	  setRules("h3", {indent : false, breakAfterOpen : false, breakBeforeClose : false} ); 
	 	  setRules("h4", {indent : false, breakAfterOpen : false, breakBeforeClose : false} ); 
	 	  setRules("h5", {indent : false, breakAfterOpen : false, breakBeforeClose : false} ); 
	 	  setRules("div", {indent : false, breakAfterOpen : false, breakBeforeClose : false} ); 
	 	  setRules("table", {indent : false, breakAfterOpen : false, breakBeforeClose : false} ); 
	 	  setRules("tr", {indent : false, breakAfterOpen : false, breakBeforeClose : false} ); 
	 	  setRules("td", {indent : false, breakAfterOpen : false, breakBeforeClose : false} ); 
	 	  setRules("iframe", {indent : false, breakAfterOpen : false, breakBeforeClose : false} ); 
	 	  setRules("li", {indent : false, breakAfterOpen : false, breakBeforeClose : false} ); 
	 	  setRules("ul", {indent : false, breakAfterOpen : false, breakBeforeClose : false} ); 
	 	  setRules("ol", {indent : false, breakAfterOpen : false, breakBeforeClose : false} ); 
    } 
}); 