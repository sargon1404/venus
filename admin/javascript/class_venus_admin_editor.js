/**
* Inserts a div 2 columns in the editor
* @param {string} editor_id The id of the editor
*/
/*venus_editor.prototype.insert_div_columns2 = function(editor_id)
{
	var code = '<div class="columns">\
	<div class="columns-row">\
	<div class="column" style="width:50%">' + venus.strings['text'] + '</div>\
	<div class="column" style="width:50%">' + venus.strings['text'] + '</div>\
	<div class="clear"></div>\
	</div>\
	</div><br />';

	venus.editor.insert_text(editor_id, code);
}*/

/**
* Inserts a div 3 columns in the editor
* @param {string} editor_id The id of the editor
*/
/*venus_editor.prototype.insert_div_columns3 = function(editor_id)
{
	var code = '<div class="columns">\
	<div class="columns-row">\
	<div class="column" style="width:33%">' + venus.strings['text'] + '</div>\
	<div class="column" style="width:33%">' + venus.strings['text'] + '</div>\
	<div class="column" style="width:33%">' + venus.strings['text'] + '</div>\
	</div>\
	</div><br />';

	venus.editor.insert_text(editor_id, code);
}*/

/*venus_editor.prototype.insert_table = function(editor_id, class_name)
{
	var rows = 3;
	var code = '<table class="' + class_name + '" style="width:50%">\
	<tr>\
		<th>' + venus.strings['heading'] + '1</th>\
		<th>' + venus.strings['heading'] + '2</th>\
	</tr>';

	var j = 1;
	for(var i = 0;i < rows;i++)
	{
		code+= '	<tr>\
			<td>' + venus.strings['cell'] + j + '</td>\
			<td>' + venus.strings['cell'] + j + '</td>\
		</tr>';
		j++;
	}

	code+= '</table>';

	venus.editor.insert_text(editor_id, code);
}*/

/**
* Inserts the read_more_start tag in the editor with id=editor_id
* @param {string} editor_id The id of the editor
*/
/*venus_editor.prototype.insert_read_more_start = function(editor_id)
{
	var code = '<span class="venus-read-more-start">&nbsp;</span>';
	if(this.is_bbcode())
		code = '[venus-read-more-start]';

	venus.editor.insert_text(editor_id, code);
}*/

/**
* Inserts the read_more_end tag in the editor with id=editor_id
* @param {string} editor_id The id of the editor
*/
/*venus_editor.prototype.insert_read_more_end = function(editor_id)
{
	var code = '<span class="venus-read-more-end">&nbsp;</span>';
	if(this.is_bbcode())
		code = '[venus-read-more-end]';

	venus.editor.insert_text(editor_id, code);
}*/

/**
* Inserts the page_break tag in the editor with id=editor_id
* @param {string} editor_id The id of the editor
*/
/*venus_editor.prototype.insert_page_break = function(editor_id)
{
	var code = '<span class="venus-page-break">&nbsp;</span>';
	if(this.is_bbcode())
		code = '[venus-page-break]';

	venus.editor.insert_text(editor_id, code);
}*/

/**
* Toggles an editor between wysiwyg and textarea
* @param {string} editor_id The id of the editor
* @param {int} [width] The width of the editor
* @param {int} [height] The height of the editor
*/
/*venus_editor.prototype.toggle_editor = function(editor_id, width, height)
{
	this.toggle_wysiwyg_editor(editor_id, width, height)
}*/

/**
* Toggles the editor's toolbar
* @param {string} editor_id The id of the editor
*/
/*venus_editor.prototype.toggle_toolbar = function(editor_id)
{
	this.name = editor_id;

	var obj = venus.get('admin-editor-toolbar');
	var container_width = venus.get('container').offsetWidth;

	venus.toggle(obj, 'block');

	var window_width = Math.max(document.documentElement.offsetWidth, document.body.offsetWidth);
	var x = (window_width  - container_width)/2 - obj.offsetWidth -10;
	if(x < 0)
		x = 5;

	obj.style.left = x + 'px';
}*/