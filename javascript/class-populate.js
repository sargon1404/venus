/**
* The Populate Class
* @author Venus-CMS
* @property {int} min_chars The number of min. chars the user must input for the populate options to become visible
* @property {int} per_row The number of options to show per row
*/
class VenusPopulate {
	constructor () {
		this.obj = null;
		this.populate = null;
		this.options = [];
		this.is_visible = false;

		this.min_chars = 3;
		this.per_row = 5;
	}

	/**
	* Inits the populate object
	* @private
	*/
	init () {
		if (this.obj) {
			return;
		}

		let html = '\
			<div id="populate">\
				<table class="data">\
				</table>\
			</div>';

		jQuery('body').append(html);

		this.obj = venus.get('populate');
		this.obj.hide();

		this.populate = {
			element: null,
			table: this.obj.find('table')
		};
	}

	/**
	* Opens the populate popup
	* @param {string|object} parent_element The parent element to which the populate popup will be attached. Usually this
	* @param {array} options The options to show. Array in the format: [{value: value, name: name, on_click: function},{}]
	* @param {function} filter_func Function which should be invoked to filter the options, if any
	* @return {this}
	*/
	open (parent_element, options, filter_func) {
		let obj = venus.get(parent_element);
		let chars = obj.val().length;

		if (chars < this.min_chars) {
			return this;
		}

		if (filter_func) {
			options = filter_func(options, obj.val());
		}

		this.show(obj, options);

		return this;
	}

	/**
	* Populates with data from an url
	* @param {event} event The event
	* @param {string|object} parent_element The parent element to which the populate popup will be attached
	* @param {string} url The url which will return the options
	* @return {this}
	*/
	openUrl (parent_element, url) {
		let obj = venus.get(parent_element);
		let chars = obj.val().length;

		if (chars < this.min_chars) {
			return this;
		}

		let self = this;

		venus.ajax.getUrl(url, function (text) {
			let options = venus.decode(text);

			self.show(parent_element, options);
		});

		return this;
	}

	/**
	* Shows the populate data
	* @param {string|object} parent_element The parent element to which the populate popup will be attached
	* @param {array} options The options to show. Array in the format: [{value: value, name: name, on_click: function},{}]
	* @private
	*/
	show (parent_element, options) {
		this.init();

		let obj = venus.get(parent_element);
		this.is_visible = true;
		this.populate.element = obj;
		this.populate.table.html(this.getOptionsHtml(options));

		// temporarily show the #populate object, so we can get the position of the popup
		this.obj.css({visibility: 'hidden'}).show();

		let pos = venus.getPosition(parent_element, this.obj);
		this.obj.css({left: pos.x + 'px', top: pos.y + 'px'});

		this.obj.css({visibility: 'visible'}).hide();

		this.showObj();
	}

	/**
	* @private
	*/
	showObj () {
		this.obj.show();
	}

	/**
	* Closes the populate popup
	* @private
	*/
	close () {
		if (!this.obj || !this.is_visible) {
			return;
		}

		this.is_visible = false;

		this.hideObj();
	}

	/**
	* @private
	*/
	hideObj () {
		this.obj.hide();
	}

	/**
	* Builds the html code for the options
	* @param {array} options The options
	* @return {string} The html code
	* @private
	*/
	getOptionsHtml (options) {
		this.options = options;

		let rows = [];
		let row_options = options.slice(0);

		while (row_options.length > 0) {
			rows.push(row_options.splice(0, this.per_row));
		}

		let html = '';
		let index = 0;

		for (let i = 0; i < rows.length; i++) {
			let cells = rows[i];

			html += '<tr>';

			for (let j = 0; j < cells.length; j++) {
				let cell = cells[j];

				html += '<td><a href="javascript:void(0)" onclick="venus.populate.select(' + index + ')">' + cell.name + '</a></td>';
				index++;
			}

			html += '</tr>';
		}

		return html;
	}

	/**
	* Function called when an option is selected
	* @param {int} The option's index
	* @private
	*/
	select (index) {
		if (!this.options[index]) {
			return;
		}

		let option = this.options[index];

		// call the on click callback
		if (option.on_click) {
			let fn = option.on_click;
			let callback = null;

			if (this[fn]) {
				callback = this[fn];
			} else if (window[fn]) {
				callback = window[fn];
			}

			if (callback) {
				callback(option, this.populate.element);
			}
		}

		this.populate.element.val(option.value);

		this.close();
	}

	/**
	* Populates with users
	* @param {event} event The event
	* @param {string|object} element Either the element's id or an object to which the populate popup will be attached
	* @param {string} [position=bottom-left] The position of the populate popup
	*/
	/* showUsers(event, element, position)
	{
		if(!element.value)
		{
			venus.set_value(element.id + '_uid', 0);
			return;
		}

		this.show(event, element, 'get_users', '', true, position);
	} */

	/**
	* Populates with pages
	* @param {event} event The event
	* @param {string|object} element Either the element's id or an object to which the populate popup will be attached
	* @param {string} [position=bottom-left] The position of the populate popup
	*/
	/* showPages(event, element, position)
	{
		if(!element.value)
		{
			venus.set_value(element.id + '_pid', 0);
			return;
		}

		this.show(event, element, 'get_pages', '', true, position);
	} */
}
