/**
* The Modals Class
* @author Venus-CMS
*/
class VenusModals
{

	/**
	* @property {bool} are_enabled True if modals are enabled
	*/
	constructor()
	{
		this.are_enabled = false;
	}

	/**
	* Enables the modals
	* @param {string|object} [element] Optional element over which the tooltips will be shown. If not specified, document is used
	* @return {this}
	*/
	enable(element)
	{
		if(!element)
		{
			if(this.are_enabled)
				return this;
		}

		this.are_enabled = true;

		var self = this;
		venus.ready(function(){

			self.init(element);

		});

		return this;
	}

	/**
	* Inits the modals
	* Attaches the messages to the elements which have [data-message-message] | [data-message-error]
	* @param {string|object} [element] Optional element over which the alerts will be enabled. If not specified, document is used
	* @private
	*/
	init(element)
	{
		venus.getSelector('[data-confirm]').each(function(){

			var obj = jQuery(this);
			obj.off('click').click(function(e){

				e.preventDefault();

				var title = obj.attr('data-title') || null;
				var text = obj.attr('data-confirm');

				venus.confirm.open(text, function(){

					obj.off('click');
					obj[0].click();

				}, null, {title: title});

			});

		});
	}

}

/**
* The Modal Class
* @author Venus-CMS
*/
class VenusModal
{

	constructor()
	{
		this.overlay_obj = null;
		this.obj = null;
		this.modal = null;
		this.buttons = [];

		this.message = new VenusMessageModal;
		this.error = new VenusErrorModal;
		this.warning = new VenusWarningModal;
		this.notification = new VenusNotificationModal;
		this.confirm = new VenusConfirmModal;
	}

	/**
	* Inits the modal
	* @private
	*/
	init()
	{
		if(this.obj)
			return;

		var html = '\
			<div id="modals-overlay">\
				<div id="modals">\
					<div id="modal">\
						<div class="modal-title">\
							<a href="javascript:void(0)" onclick="venus.modal.close()" class="close"></a>\
							<div class="modal-icon"></div>\
							<h1></h1>\
						</div>\
						<div class="modal-content">\
							<div class="modal-image"></div>\
							<div class="modal-text"></div>\
						</div>\
						<div class="modal-buttons"></div>\
					</div>\
				</div>\
			</div>';

		jQuery('body').append(html);

		this.overlay_obj = venus.get('modals-overlay');
		this.obj = venus.get('modal');

		this.modal = {
			title: this.obj.find('.modal-title h1'),
			icon: this.obj.find('.modal-icon'),
			text: this.obj.find('.modal-text'),
			buttons: this.obj.find('.modal-buttons')
		}

		this.obj.click(function(e){
			e.stopPropagation()
		});

		//close the modal if the overlay is clicked
		var self = this;
		this.overlay_obj.click(function(e){
				self.close();
				e.stopPropagation()
		});

		this.overlay_obj.hide();
		this.obj.hide();
	}

	/**
	* Opens a modal
	* @param {string} text The modal's text
	* @param {string} class_name The modal's class
	* @param {object} [options] The options. Supported options: {title: <title>, icon: <icon>, width: <width>, height: <height>}
	* @param {array} [buttons] The modal's buttons. Array in the format: [{value: <value>, on_click: <on_click>, class_name: <class_name>}]
	* @return {this}
	*/
	open(text, class_name, options, buttons)
	{
		this.init();

		this.set(text, class_name, options, buttons);

		this.show();

		return this;
	}

	/**
	* Sets the modal's data
	* @private
	*/
	set(text, class_name, options, buttons)
	{
		this.buttons = [];

		class_name = class_name || 'modal';
		buttons = buttons || this.getDefaultButtons();
		options = options || {};
		options.title = options.title || '&nbsp;';
		options.icon = options.icon || '';

		if(options.icon)
			options.icon = '<img src="' + venus.theme.getImage(options.icon, 'modals') + '" />';

		this.obj.prop('class', class_name);
		this.modal.icon.html(options.icon);
		this.modal.title.html(options.title);
		this.modal.text.html(text);
		this.modal.buttons.html(this.getButtons(buttons));
		this.prepareButtons();

		if(options.width)
			this.obj.width(options.width);
		if(options.height)
			this.obj.height(options.height);
	}

	/**
	* Returns the default buttons
	* @return {array}
	* @private
	*/
	getDefaultButtons()
	{
		var self = this;
		return [{value: venus.lang.strings.ok, on_click: function(){self.close()}}];
	}

	/**
	* Returns the html code for buttons
	* @return {string}
	* @private
	*/
	getButtons(buttons)
	{
		if(!buttons)
			return '';

		var html = '';
		var index = 0;
		for(var i = 0; i < buttons.length; i++)
		{
			var id = venus.generateId('modal-button');
			var button = buttons[i];

			html+= '<input type="button" id="' + id + '" value="' + button.value + '">';

			if(button.on_click)
			{
				this.buttons[index] = {
					id: id,
					on_click: button.on_click
				};
				index++;
			}
		}

		return html;
	}

	/**
	* Prepares the buttons; sets the onclick events
	* @private
	*/
	prepareButtons()
	{
		if(!this.buttons.length)
			return;

		for(var i = 0; i < this.buttons.length; i++)
		{
			var button = this.buttons[i];

			venus.get(button.id).click(button.on_click);
		}
	}

	/**
	* Displays the currently opened modal
	* @private
	*/
	show()
	{
		if(!this.obj)
			return;

		this.showObj();
	}

	/**
	* @private
	*/
	showObj()
	{
		this.overlay_obj.show();
		this.obj.show();
	}

	/**
	* Closes the currently opened modal
	* @return {this}
	*/
	close()
	{
		if(!this.obj)
			return;

		this.hideObj();

		return this;
	}

	/**
	* @private
	*/
	hideObj()
	{
		this.obj.hide();
		this.overlay_obj.hide();
	}

}