/**
* The Dialog Class
* @author Venus-CMS
*/
class VenusDialog
{

	/**
	* @property {object} dialogs Object containing the data of the existing dialogs
	* @property {object} dialog The data of the currently loaded dialog
	*/
	constructor()
	{
		this.overlay_obj = null;
		this.dialogs_obj = null;
		this.dialogs = {};
		this.dialog = null;

		this.scroll_by = 50;
		this.scroll_interval_handle = 0;
	}

	/**
	* Returns the id under which the dialog's content will be stored inside #dialogs
	* @param {string} id The element's id
	* @return {string} The id
	* @private
	*/
	getId(id)
	{
		if(!id)
			id = venus.generateId();

		return 'dialog-id-' + id;
	}

	/**
	* Returns the id under of a preloaded dialog
	* @param {string} id The element's id
	* @return {string} The id
	* @private
	*/
	getPreloadedId(id)
	{
		return 'dialog-cnt-' + id;
	}

	/**
	* Inits the dialog
	* @private
	*/
	init()
	{
		if(this.dialogs_obj)
			return;

		var html = '\
			<div id="dialogs-overlay">\
				<div id="dialogs"></div>\
			</div>';

		jQuery('body').append(html);

		this.overlay_obj = venus.get('dialogs-overlay');
		this.dialogs_obj = venus.get('dialogs');

		//close the dialog if the overlay is clicked
		var self = this;
		this.overlay_obj.click(function(e){
				self.close();
				e.stopPropagation()
		});

		this.overlay_obj.hide();
	}

	/**
	* Sets the currently loaded dialog
	* @param {string} id The dialog's id
	* @private
	*/
	set(id)
	{
		if(!this.dialogs[id])
		{
			this.dialog = null;
			return;
		}

		this.dialog = this.dialogs[id];
	}

	/**
	* Opens the content of an element in a dialog
	* @param {string|object} element The element to display in the dialog
	* @param {object} [options] The options. Supported options: {title: title, icon: icon, width: width, height: height, show_scroll: bool}
	* @return {this}
	*/
	openElement(element, options)
	{
		this.init();

		var obj = venus.get(element);
		var id = this.getId(obj.prop('id'));

		//add the dialog, if it doesn't exist
		if(!this.dialogs[id])
			this.add(id, obj, options);
		else
			this.set(id);

		this.show();

		return this;
	}

	/**
	* Opens an url in a dialog
	* @param {string} url The url to open
	* @param {id} url The id under which the dialog's content will be stored
	* @param {object} [options] The options. Supported options: {title: title, icon: icon, width: width, height: height, show_scroll: bool}
	* @return {this}
	*/
	openUrl(url, id, options)
	{
		this.init();

		id = this.getId(id);

		//add the dialog, if it doesn't exist
		if(!this.dialogs[id])
		{
			venus.loading.show();

			//create the html code for the iframe, then add it
			var html = '<iframe src="' + url + '"></iframe>';

			this.add(id, html, options);

			//dirty but effective hack:
			//show the dialog & overlay, but keep overlay's visibility hidden until the dialog has loaded, then hide it again so we can resize the iframe
			this.dialog.overlay.css({'visibility' : 'hidden'}).show();
			this.dialog.obj.show();

			var self = this;
			this.dialog.iframe.on('load', function(){

				//resize the dialog &iframe
				var height = Math.max(this.contentWindow.document.body.scrollHeight, this.contentWindow.document.documentElement.offsetHeight);
				self.resize(height);

				//hide the dialog & overlay again; then show it using show(), so we can display it using effects
				self.dialog.overlay.css({'visibility' : 'visible'}).hide();
				self.dialog.obj.hide();

				self.show();

				venus.loading.hide();

			});
		}
		else
		{
			this.set(id);
			this.show();
		}

		return this;
	}

	/**
	* Opens a dialog script from the dialogs dir
	* @param {string} name The name of the dialog to open
	* @param {object} [options] The options. Supported options: {title: title, icon: icon, width: width, height: height, show_scroll: bool}
	* @param {array|object} [params] Extra params to pass to the dialog's url
	* @param {string} [alias] The dialog's alias. Must be specified if multiple dialogs of the same type are opened on a page, with each dialog of that type having it's own alias
	* @return {this}
	*/
	open(name, options, params, alias)
	{
		this.init();

		if(!alias)
			alias = name;

		var id = name + '-' + alias;
		var preloadedId = this.getPreloadedId(id);

		//was the dialog preloaded?
		if(venus.exists(preloadedId))
			this.openElement(preloadedId);
		else
		{
			var url = venus.uri.convert(this.getUrl(name, alias, params));
			this.openUrl(url, id, options);
		}

		return this;
	}

	/**
	* Returns the url of a dialog
	* @param {string} name The name of the dialog
	* @param {string} alias The dialog's alias
	* @param {array|object} [params] Extra params to pass to the dialog's url
	* @return {string} The url
	* @private
	*/
	getUrl(name, alias, params)
	{
		var url = venus.assets_url + 'dialog.php?dialog_name=' + encodeURI(name) + '&dialog_alias=' + encodeURI(alias);

		if(params)
			url+= '&' + venus.uri.buildParams(params);

		return url;
	}

	/**
	* Adds the content of a dialog to the dialogs list
	* @param {string} id The id under which the content will be stored
	* @param {string} content The content
	* @param {object} [options] The options
	* @private
	*/
	add(id, content, options)
	{
		options = options || {};
		options.title = this.getTitle(content, options.title);
		options.icon = this.getIcon(options.icon);
		options.show_scroll = options.show_scroll || false;

		var scroll_top = '';
		var scroll_bottom = '';
		if(options.show_scroll)
		{
			scroll_top = '<div class="dialog-scroll-top"></div>';
			scroll_bottom = '<div class="dialog-scroll-bottom"></div>';
		}

		var html = '\
			<div class="dialog" id="' + id + '">\
				<div class="dialog-title">\
					<a href="javascript:void(0)" onclick="venus.dialog.close()" class="close"></a>' + options.icon + '<h1>' + options.title + '</h1>\
				</div>\
				' + scroll_top + '\
				<div class="dialog-content"></div>\
				' + scroll_bottom + '\
			</div>';

		//append the dialog's html code to the dialogs object
		this.dialogs_obj.append(html);

		var dialog = venus.get(id);
		var content_obj = dialog.find('.dialog-content');

		this.append(content, content_obj);;

		if(options.width)
			dialog.width(options.width);
		if(options.height)
			content_obj.height(options.height);

		dialog.hide();

		//store the dialog's objects in this.dialogs for faster access
		this.dialogs[id] = {
			id: id,
			overlay: this.overlay_obj,
			obj: dialog,
			content: content_obj,
			iframe: content_obj.find('iframe')
		};

		this.set(id);

		this.prepare(options.show_scroll);
	}

	/**
	* Appends the content to the content obj
	* @private
	*/
	append(content, content_obj)
	{
		if(typeof content == 'object')
		{
			content_obj.append(content);
			content.show();
		}
		else
		{
			content_obj.html(content);
			venus.initHtml(content_obj);
		}
	}

	/**
	* Returns the title of the dialog
	* @private
	*/
	getTitle(content, title)
	{
		if(typeof content == 'object')
			title = title || content.attr('data-title');

		title = title || '&nbsp;';

		return title;
	}

	/**
	* Returns the title of the dialog
	* @private
	*/
	getIcon(icon)
	{
		icon = icon || '';
		if(icon)
			icon = '<img src="' + venus.theme.getImage(icon, 'dialogs') + '">';

		return icon;
	}

	/**
	* Prepares the dialog's events
	* @param {bool} show_scroll If true, will prepare the scrolling areas
	* @private
	*/
	prepare(show_scroll)
	{
		var self = this;

		this.dialog.obj.click(function(e){
			e.stopPropagation()
		});

		if(show_scroll)
		{
			var scroll_top_obj = this.dialog.obj.find('.dialog-scroll-top');
			var scroll_bottom_obj = this.dialog.obj.find('.dialog-scroll-bottom');

			//scroll the dialog content-if scroll bars are visible- if an item is dragged above/below the content area
			scroll_top_obj.click(function(){
				self.scrollToTop();
			});

			scroll_bottom_obj.click(function(){
				self.scrollToBottom();
			});

			scroll_top_obj.on('dragenter', function(){

				self.scrollTop();
				self.scroll_interval_handle = setInterval(self.scrollTop, 100);

				return false;

			});

			scroll_top_obj.on('dragleave', function(){

				if(self.scroll_interval_handle)
					clearInterval(self.scroll_interval_handle);

				return false;

			});

			scroll_bottom_obj.on('dragenter', function(){

				self.scrollBottom();
				self.scroll_interval_handle = setInterval(self.scrollBottom, 100);

				return false;

			});

			scroll_bottom_obj.on('dragleave', function(){

				if(self.scroll_interval_handle)
					clearInterval(self.scroll_interval_handle);

				return false;

			});
		}
	}

	/**
	* Resizes the height of the currently opened dialog to match the size of the iframe
	* @param {int} height The height to resize to
	* @private
	*/
	resize(height)
	{
		var max_height = this.dialog.obj.outerHeight();

		if(height < max_height)
		{
			this.dialog.iframe.height(height);
			this.dialog.content.height(height + 15);
		}
	}

	/**
	* Displays the currently opened dialog
	* @private
	*/
	show()
	{
		if(!this.dialog)
			return;

		this.showObj();
	}

	/**
	* @private
	*/
	showObj()
	{
		this.dialog.overlay.show();
		this.dialog.obj.show();
	}

	/**
	* Closes the currently opened dialog,if any
	* @return {this}
	*/
	close()
	{
		if(!this.dialog)
			return;

		this.hideObj();

		return this;
	}

	/**
	* @private
	*/
	hideObj()
	{
		this.dialog.obj.hide();
		this.dialog.overlay.hide();
	}

	/**
	* Reloads a dialog, by deleting it's previous html code from #dialogs
	* @param {string} id The id of the dialog to reload
	* @return {this}
	*/
	reloadById(id)
	{
		id = this.getId(id);

		this.dialogs[id] = null;

		venus.get(id).remove();

		return this;
	}

	/**
	* Reloads a dialog
	* @param {string} name The dialog's name
	* @param {string} [alias] The dialog's alias
	* @return {this}
	*/
	reload(name, alias)
	{
		if(!alias)
			alias = name;

		var id = this.getId(name + '-' + alias);

		this.reloadById(id);

		return this;
	}

	/**
	* Scrolls the dialog to top if the user drags a file inside the top scroll area
	* @private
	*/
	scrollTop()
	{
		var self = venus.dialog;

		if(!self.dialog.iframe)
			return;

		var iframe = self.dialog.iframe[0];
		var top = iframe.contentDocument.documentElement.scrollTop || iframe.contentDocument.body.scrollTop;

		if(top <= 0)
		{
			if(self.scroll_interval_handle)
				clearInterval(self.scroll_interval_handle);

			return;
		}

		top = top - self.scroll_by;
		if(top < 0)
			top = 0;

		iframe.contentWindow.scrollTo(0, top);
	}

	/**
	* Scrolls the dialog to bottom if the user drags a file inside the top scroll area
	* @private
	*/
	scrollBottom()
	{
		var self = venus.dialog;

		if(!self.dialog.iframe)
			return;

		var iframe = self.dialog.iframe[0];
		var top = iframe.contentDocument.documentElement.scrollTop || iframe.contentDocument.body.scrollTop;
		var max = iframe.contentDocument.documentElement.scrollHeight || iframe.contentDocument.body.scrollHeight;

		if(top > max)
		{
			if(self.scroll_interval_handle)
				clearInterval(self.scroll_interval_handle);

			return;
		}

		top = top + self.scroll_by;

		if(top > max)
			return;

		iframe.contentWindow.scrollTo(0, top);
	}

	/**
	* Scrolls the dialog to top if the user clicks the top scroll area
	* @private
	*/
	scrollToTop()
	{
		if(!this.dialog.iframe)
			return;

		var iframe = this.dialog.iframe[0];
		iframe.contentWindow.scrollTo(0, 0);
	}

	/**
	* Scrolls the dialog to bottom if the user clicks the top scroll area
	* @private
	*/
	scrollToBottom()
	{
		if(!this.dialog.iframe)
			return;

		var iframe = this.dialog.iframe[0];
		var height = iframe.contentDocument.documentElement.scrollHeight || iframe.contentDocument.body.scrollHeight;

		iframe.contentWindow.scrollTo(0, height);
	}

}