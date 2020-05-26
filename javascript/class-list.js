/**
* The List Class
* Shows a list of links when the parent element is clicked
* @author Venus-CMS
*/
class VenusList {
	constructor () {
		this.obj = null;
		this.is_visible = false;
	}

	/**
	* Opens the list
	* @param {string|object} element The element to show in the list
	* @param {string|object} parent_element The parent element to which the list will be attached. Usually this
	* @param {event} event If specified, will stop the propagation
	* @return {this}
	*/
	open (element, parent_element, event) {
		if (event) {
			event.stopPropagation();
		}

		this.show(element, parent_element);

		return this;
	}

	/**
	* Adds the content as a list
	* @param {string} content The content
	* @private
	*/
	add (content) {
		this.obj.html(content);
	}

	/**
	* Shows the list
	* @param {string|object} obj The object to which the list is attached
	* @private
	*/
	show (element, parent_element) {
		this.obj = venus.get(element);

		// temporarily show the object, so we can get it's position
		this.obj.addClass('entry-list');
		this.obj.css({visibility: 'hidden'}).show();
		let pos = venus.getPosition(parent_element, this.obj);
		this.obj.css({left: pos.x + 'px', top: pos.y + 'px'});

		this.obj.css({visibility: 'visible'}).hide();

		this.is_visible = true;

		this.showObj();
	}

	/**
	* @private
	*/
	showObj () {
		this.obj.show();
	}

	/**
	* Closes the list
	* @return {this}
	*/
	close () {
		if (!this.is_visible) {
			return;
		}

		this.is_visible = false;
		this.hideObj();

		return this;
	}

	/**
	* @private
	*/
	hideObj () {
		this.obj.hide();
	}

	/**
	* Toggles the list
	* @param {string|object} element The element to show in the list
	* @param {string|object} parent_element The parent element to which the list will be attached. Usually this
	* @param {event} event If specified, will stop the propagation
	* @return {this}
	*/
	toggle (element, parent_element, event) {
		if (event) {
			event.stopPropagation();
		}

		if (this.is_visible) {
			this.close();
		} else {
			this.open(element, parent_element);
		}

		return this;
	}
}
