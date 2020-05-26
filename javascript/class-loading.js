/**
* The Loading Class
* Shows a loading icon over a specified area
* @author Venus-CMS
*/
class VenusLoading {
	constructor () {
		this.obj = null;
	}

	/**
	* Inits the loading object
	* @private
	*/
	init () {
		if (this.obj) {
			return;
		}

		let html = '\
			<div id="loading-overlay">\
				<div id="loading-container"><div id="loading-image"></div></div>\
			</div>';

		jQuery('body').append(html);

		this.obj = venus.get('loading-overlay');
		this.obj.hide();
	}

	/**
	* Shows the loading screen
	* @param {string|object} [element] If specified will show the animation over element [id | object]. If not specified, will show it on the entire screen
	* @return {this}
	*/
	show (element) {
		this.init();

		if (element) {
			let obj = venus.get(element);
			let pos = obj.position();

			this.obj.css({position: 'absolute', left: pos.left + 'px', top: pos.top + 'px', width: obj.innerWidth(), height: obj.innerHeight()});
		} else {
			this.obj.css({position: 'fixed', left: '0px', top: '0px', width: '100%', height: jQuery(window).height()});
		}

		this.showObj();

		return this;
	}

	/**
	* @private
	*/
	showObj () {
		this.obj.show();
	}

	/**
	* Shows the loading screen over the venus.content_obj element
	* @return {this}
	*/
	showOverContent () {
		this.show(venus.content_obj);

		return this;
	}

	/**
	* Shows the loading screen over the venus.main_obj element
	* @return {this}
	*/
	showOverMain () {
		this.show(venus.main_obj);

		return this;
	}

	/**
	* Hides the loading screen
	* @return {this}
	*/
	hide () {
		if (!this.obj) {
			return;
		}

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
	* Shows a small loading icon
	* @param {string|object} element The element inside which the icon is shown
	*/
	showIcon (element) {
		venus.get(element).addClass('loading-small').show();
	}

	/**
	* Hides a loading icon
	* @param {string|object} element The element inside which the icon was shown
	*/
	hideIcon (element) {
		venus.get(element).removeClass('loading-small').hide();
	}

	/**
	* Shows a small loading icon over an input control
	* @param {string|object} input The input control
	*/
	showInput (input) {
		venus.get(input).addClass('loading-input');
	}

	/**
	* Hides the loading icon over an input control
	* @param {string|object} input The input control
	*/
	hideInput (input) {
		venus.get(input).removeClass('loading-input');
	}
}
