/**
* The Editor Class
* @author Venus-CMS
* @property {string} type The editor's type
* @property {string} name The editor's name
*/
class VenusEditor {
	constructor () {
	this.type = 'bbcode';
	this.name = '';
	}
	
	/**
	* Determines if the current editor is a bbcode editor
	* @return {bool}
	*/
	isBBCode () {
		if (this.type == 'bbcode') {
			return true;
		}
			
		return false;
	}
	
	/**
	* Resizes the editor
	* @param {string} editor_id The editor to resize
	* @param {int} height The height to resize the editor too
	* @param {int} padding The padding
	*/
	resizeHeight (editor_id, height, padding) {
		if (String(height).indexOf('%') != -1) {
			height = parseInt(height) * venus.getWindowHeight() / 100;
			
			height -= padding;
		}
		else {
			height = parseInt(height);
		}
	
		venus.get(editor_id).style.height = height + 'px';
		
		return this;
	}
}




/**
* Returns the preview of editor with id=editor_id
* @param {string} editor_id The id of the editor
* @param {string} preview_title The title of the dialog in which the preview is displayed
* @param {string} preview_class Class to be applied to the generated preview text
* @param {string} token_value The venus token
*/
/*venus_editor.prototype.preview = function(editor_id, preview_title, token_value)
{
	var text = this.get_text(editor_id);
	if(!preview_title)
		preview_title = venus.strings['preview'];

	venus.ajax.post('preview', {text:text}, {token_value:token_value}, function(preview_html){

		venus.dialog.reload('preview');
		venus.dialog.open('preview', preview_title, 'preview.png');
	});
}*/

/**
* Returns the content/text of editor_id
* @param {string} editor_id The id of the editor
* @param {bool} [is_input=false] If true,will return the text of an input rather than an editor
*/
/*venus_editor.prototype.get_text = function(editor_id, is_input)
{
	if(!editor_id)
		editor_id = 'venus_editor';

	if(!is_input)
	{
		if(this.type == 'wysiwyg')
		{
			var text = this.get_wysiwyg_text(editor_id);
			if(text !== false)
				return text;
		}
		else if(this.type == 'bbcode')
		{
			var text = this.get_bbcode_text(editor_id);
			if(text !== false)
				return text;
		}
	}

	return this.get_textarea_text(editor_id);
}*/

/**
* Updates the content/text of editor_id with text
* @param {string} editor_id The id of the editor
* @param {string} text The text to update the editor with
* @param {bool} [is_input=false] If true,will return the text of an input rather than an editor
*/
/*venus_editor.prototype.update_text = function(editor_id, text, is_input)
{
	if(!editor_id)
		editor_id = 'venus_editor';

	if(!is_input)
	{
		if(this.type == 'wysiwyg')
		{
			if(this.update_wysiwyg_text(editor_id, text) !== false)
				return;
		}
		else if(this.type == 'bbcode')
		{
			if(this.update_bbcode_text(editor_id, text) !== false)
				return;
		}
	}

	this.update_textarea_text(editor_id, text);
}*/


/**
* Appends text to editor_id
* @param {string} editor_id The id of the editor
* @param {string} text The text to append to the editor
* @param {bool} [is_input=false] If true,will return the text of an input rather than an editor
*/
/*venus_editor.prototype.append_text = function(editor_id, text, is_input)
{
	if(!editor_id)
		editor_id = 'venus_editor';

	if(!is_input)
	{
		if(this.type == 'wysiwyg')
		{
			if(this.append_wysiwyg_text(editor_id, text) !== false)
				return;
		}
		else if(this.type == 'bbcode')
		{
			if(this.append_bbcode_text(editor_id, text) !== false)
				return;
		}
	}

	this.append_textarea_text(editor_id, text);
}*/

/**
* Inserts text at cursor position into editor_id
* @param {string} editor_id The id of the editor
* @param {string} text The text to insert
* @param {bool} [is_input=false] If true,will return the text of an input rather than an editor
*/
/*venus_editor.prototype.insert_text = function(editor_id, text, is_input)
{
	if(!editor_id)
		editor_id = 'venus_editor';

	if(!is_input)
	{
		if(this.type == 'wysiwyg')
		{
			if(this.insert_wysiwyg_text(editor_id, text) !== false)
				return;
		}
		else if(this.type == 'bbcode')
		{
			if(this.insert_bbcode_text(editor_id, text) !== false)
				return;
		}
	}

	this.insert_textarea_text(editor_id, text);
}
*/
/**
* Focuses the editor
* @param {string} editor_id The id of the editor
*/
/*venus_editor.prototype.focus = function(editor_id)
{
	var obj = venus.get(editor_id);
	obj.focus();
}*/

/**
* TODO: Add wysiwyg type functionality for the functions below for API consistency
*/

/**
* Inserts text before and after the cursor position
* @param {string} editor_id The id of the editor
* @param {string} before_text The text to insert before the cursor
* @param {string} after_text The text to insert before the cursor
*/
/*venus_editor.prototype.insert_bounding_text = function(editor_id, before_text, after_text)
{
	if(!editor_id)
		editor_id = 'venus_editor';

	var obj = venus.get(editor_id);

	var position = this.get_selected_text_position(editor_id);

	var t1 = obj.value.substr(0, position.start);
	var t2 = obj.value.substr(position.start, position.end - position.start);
	var t3 = obj.value.substr(position.end);

	obj.value = t1 + before_text + t2 + after_text + t3;
}*/

/**
* Returns the cursor position of editor_id
* @param {string} editor_id The id of the editor
*/
/*venus_editor.prototype.get_cursor_position = function(editor_id)
{
	return venus.get(editor_id).selectionStart;
}*/

/**
* Moves the cursor position of editor_id
* @param {string} editor_id The id of the editor
* @param {int} position The new position of the cursor
*/
/*venus_editor.prototype.move_cursor_position = function(editor_id, position)
{
	var obj = venus.get(editor_id);
	obj.focus();
	obj.setSelectionRange(position, position);
}*/

/**
* Returns the currently selected text
* @param {string} editor_id The id of the editor
*/
/*venus_editor.prototype.get_selected_text = function(editor_id)
{
	var obj = venus.get(editor_id);
	var pos = this.get_selected_text_position(obj);

	return obj.value.substr(pos.start, pos.end - pos.start);
}*/

/**
* Returns the position of the currently selected text
* @param {string} editor_id The id of the editor
*/
/*venus_editor.prototype.get_selected_text_position = function(editor_id)
{
	var obj = venus.get(editor_id);

	return {start:obj.selectionStart, end:obj.selectionEnd};
}*/

/**
* @private
*/
/*venus_editor.prototype.set_webstorage_value = function(name, value)
{
	venus.webstorage.set(name, value);
}*/

/**
* Saves the content of the editors to the webstorage at certain intervals
*/
/*venus_editor.prototype.save = function(editors, save_interval)
{
	this.save_editors = editors;
	if(!this.save_editors)
		return;
	if(!save_interval)
		return;

	this.save_interval = save_interval * 1000;

	venus.add_onload(function()
	{
		for(var i in venus.editor.save_editors)
		{
			var editor_id = venus.editor.save_editors[i][0];
			var item_id = venus.editor.save_editors[i][1];
			var timer_handle = 0;

			var stored_item_id = venus.webstorage.get(i + '_id');
			if(item_id !== stored_item_id)
				venus.webstorage.set(i + '_id', item_id);

			///load the webstorage contents for the editor,if any
			if(item_id != -1)
			{
				if(stored_item_id === null || item_id == stored_item_id)
				{
					var text = venus.webstorage.get(i);
					if(text !== null)
						venus.editor.update_text(editor_id, text);
				}
			}

			venus.editor.enable_webstorage(editor_id, i);
		}
	});
}
*/
/*venus_editor.prototype.enable_webstorage = function(editor_id, i)
{
	var self = this;

	if(this.is_wysiwyg(editor_id))
	{
		this.wysiwyg_onfocus(editor_id, function(){
		
				timer_handle = setInterval(function(){
					
					var text = self.get_text(editor_id);
					if(text === false)
						return;
						
					self.set_webstorage_value(i, text);
					
				 }, this.save_interval);
			});

		this.wysiwyg_onblur(editor_id, function(){
		
				if(timer_handle)
					clearInterval(timer_handle);
					
				timer_handle = 0;							
					 
				var text = self.get_text(editor_id);

				if(text === false)
					return;

				self.set_webstorage_value(i, text);
			});
	}
	else 
	{
		this.textarea_onfocus(editor_id, function(){
			
			timer_handle = setInterval(function(){
				
				var text = self.get_text(editor_id);
				
				if(text === false)
					return;

				self.set_webstorage_value(i, text);
				
			 }, this.save_interval);
		});
		

		this.textarea_onblur(editor_id, function(){
			
			if(timer_handle)
				clearInterval(timer_handle);
				
			timer_handle = 0;	
			
			var text = self.get_text(editor_id);

			if(text === false)
				return;

			self.set_webstorage_value(i, text);
		});
	}
}
*/
/**
* Clears the contents of editors from webstorage
*/
/*venus_editor.prototype.clear = function(editors)
{
	for(var i = 0;i < editors.length;i++)
	{
		venus.webstorage.delete(editors[i]);
		venus.webstorage.delete(editors[i] + '_id');
	}
}*/