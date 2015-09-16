CKEDITOR.plugins.add('insertcode', 
{ 
init: function(editor) 
{ 
//plugin code goes here 
var pluginName = 'Insertcode'; 
CKEDITOR.dialog.add(pluginName, this.path + 'insertcode.js'); 
editor.config.flv_path = editor.config.flv_path || (this.path); 
editor.addCommand(pluginName, new CKEDITOR.dialogCommand(pluginName)); 
editor.ui.addButton('Insertcode', 
{ 
label: '≤Â»Î¥˙¬Î', 
command: pluginName, 
icon: this.path + 'insertcode.gif' 
}); 
} 
}); 
