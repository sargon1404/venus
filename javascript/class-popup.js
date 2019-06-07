/**
* The Popup Class
* @author Venus-CMS
* @property {object} popups Object containing the data of the existing popups
* @property {int} opened The number of currently opened popups
*/
class VenusPopup
{

	constructor()
	{
		this.obj = null;
		this.popups = {};
		this.opened = 0;
	}

	/**
	* Returns the id under which the popup's content will be stored inside #popups
	* @param {string} id The element's id
	* @return {string} The id
	* @private
	*/
	getId(id)
	{
		if(!id)
			id = venus.generateId();

		return 'popup-id-' + id;
	}

	/**
	* Inits the popup
	* @private
	*/
	init()
	{
		if(this.obj)
			return;

		var html = '<div id="popups"></div>';

		jQuery('body').append(html);

		this.obj = venus.get('popups');
		this.obj.hide();
	}

	/**
	* Opens the content of an element as a popup, below parent_element
	* @param {string|object} element The element to display in the popup
	* @param {string|object} parent_element The parent element to which the popup will be attached
	* @param {object} [options] The options. Supported options: {title: title, show_bar: bool, width: width, height: height}
	* @return {this}
	*/
	open(element, parent_element, options)
	{
		this.init();

		var obj = venus.get(element);
		var id = this.getId(obj.attr('id'));

		if(!this.popups[id])
			this.add(id, obj, options);

		var popup = this.popups[id];

		//temporarily show the #popups object, so we can get the position of the popup
		if(!this.opened)
			this.obj.css({visibility: 'hidden'}).show();

		var pos = venus.getPosition(parent_element, popup.obj);
		popup.obj.css({left: pos.x + 'px', top: pos.y + 'px'});

		if(!this.opened)
			this.obj.css({visibility: 'visible'}).hide();

		this.show(id);

		return this;
	}

	/**
	* Opens the content of a preloaded dialog as a popup
	* @param {string} name The name of the dialog
	* @param {string|object} parent_element The parent element to which the popup will be attached
	* @param {string} [alias] The alias of the dialog
	* @param {object} [options] The options. Supported options: {title: title, show_bar: bool, width: width, height: height}
	* @return {this}
	*/
	openDialog(name, parent_element, alias, options)
	{
		var id = name;
		if(alias)
			id+= '-' + alias;

		id = venus.dialog.getPreloadedId(id);

		this.open(id, parent_element, options);

		return this;
	}

	/**
	* Adds the content of a popup to the popups list
	* @param {string} id The id under which the content will be stored
	* @param {object} content The content
	* @param {object} [options] The options
	* @private
	*/
	add(id, content, options)
	{
		options = options || {};
		options.title = options.title || '&nbsp;';
		options.show_bar = options.show_bar || true;

		var html = '\
			<div class="popup" id="' + id + '">\
				<div class="popup-title">\
					<a href="javascript:void(0)" onclick="venus.popup.close(\'' + id + '\')" class="close"></a><h3>' + options.title + '</h3>\
				</div>\
				<div class="popup-content"></div>\
			</div>\
		';

		//append the popup's html code to the popups object
		this.obj.append(html);

		var popup = venus.get(id);
		var content_obj = popup.find('.popup-content');

		content_obj.append(content);
		content.show();

		popup.css({top: 0, left: 0});
		popup.hide();

		if(!options.show_bar)
			popup.find('.popup-title').hide();

		if(options.width)
			popup.width(options.width);
		if(options.height)
			content_obj.height(options.height);

		this.popups[id] = {
			id: id,
			obj: popup,
			visible: false
		};
	}

	/**
	* Shows a popup
	* @param {string} id The popup's id
	* @private
	*/
	show(id)
	{
		if(!this.popups[id])
			return;

		if(!this.opened)
			this.obj.show();

		this.opened++;
		this.popups[id].visible = true;

		this.showObj(this.popups[id].obj);
	}

	/**
	* @private
	*/
	showObj(obj)
	{
		obj.show();
	}

	/**
	* Closes a popup
	* @param {string} id The popup's id
	* @return {this}
	*/
	close(id)
	{
		if(!this.popups[id])
			return;

		this.opened--;
		this.popups[id].visible = false;

		this.hideObj(this.popups[id].obj, this.opened);

		return this;
	}

	/**
	* @private
	*/
	hideObj(obj, opened)
	{
		obj.hide();

		if(!opened)
			this.obj.hide();
	}

	/**
	* Toggles a popup opened/closed
	* @param {string|object} element The element to display in the popup
	* @param {string|object} parent_element The parent element to which the popup will be attached
	* @param {object} [options] The options. Supported options: {title: title, show_bar: bool, width: width, height: height}
	* @return {this}
	*/
	toggle(element, parent_element, options)
	{
		var obj = venus.get(element);
		var id = this.getId(obj.attr('id'));

		var visible = false;
		if(this.popups[id])
			visible = this.popups[id].visible;

		if(!visible)
			this.open(element, parent_element, options);
		else
			this.close(id);

		return this;
	}

	/**
	* Toggles a popup opened/closed with the contents of a preloaded dialog
	* @param {string} name The name of the dialog
	* @param {string|object} parent_element The parent element to which the popup will be attached
	* @param {string} [alias] The alias of the dialog
	* @param {object} [options] The options. Supported options: {title: title, show_bar: bool, width: width, height: height}
	* @return {this}
	*/
	toggleDialog(name, parent_element, alias, options)
	{
		var id = name;
		if(alias)
			id+= '-' + alias;

		id = venus.dialog.getPreloadedId(id);

		this.toggle(id, parent_element, options);

		return this;
	}

}