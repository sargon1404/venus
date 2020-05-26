/**
* The Inline Alerts Classes
* @author Venus-CMS
*/
class VenusAlertsInline {
	/**
	* @property {int} timeout The default timeout
	*/
	constructor () {
		this.timeout = 8;

		this.messages = new VenusMessagesInline();
		this.errors = new VenusErrorsInline();
		this.warnings = new VenusWarningsInline();
		this.notifications = new VenusNotificationsInline();
	}

	/**
	* Displays an inline message
	* @param {string} container The element inside which the message is shown
	* @param {string} text The text of the alert
	* @param {string} class_name The alert's class
	* @param {int} [timeout] The interval, in seconds, after which the alert is automatically hidden
	* @return {this}
	*/
	show (container, text, class_name, timeout) {
		let id = venus.generateId('alert-inline');

		timeout = timeout || this.timeout;
		class_name = class_name || 'alert-inline';

		let html = '\
			<div id="' + id + '" class="' + class_name + '">\
				<a href="javascript:void(0)" onclick="venus.alerts_inline.close(\'' + id + '\')" class="close-small"></a>\
				<div class="alert-inline-icon"></div>\
				<div class="alert-inline-text">' + text + '</div>\
				<div class="clear"></div>\
			</div>';

		let obj = venus.get(container);
		obj.html(html);

		// hide the alert after timeout seconds
		if (timeout) {
			setTimeout(function () {
				venus.alerts_inline.close(id);
			}, timeout * 1000);
		}

		this.showObj(obj);

		return this;
	}

	/**
	* @private
	*/
	showObj (obj) {
		obj.show();
	}

	/**
	* Closes an inline alert
	* @param {string} id The alert's id
	*/
	close (id) {
		let obj = venus.get(id);

		this.hideObj(obj, obj.parent());
	}

	/**
	* @private
	*/
	hideObj (obj, parent) {
		parent.hide();

		obj.remove();
	}
}

/**
* The Inline Messages Class
* @author Venus-CMS
*/
class VenusMessagesInline {
	constructor () {
		this.class_name = 'message-inline';
	}
}

/**
* The Inline Errors Class
* @author Venus-CMS
*/
class VenusErrorsInline {
	constructor () {
		this.class_name = 'error-inline';
	}
}

/**
* The Inline Warnings Class
* @author Venus-CMS
*/
class VenusWarningsInline {
	constructor () {
		this.class_name = 'warning-inline';
	}
}

/**
* The Inline Notifications Class
* @author Venus-CMS
*/
class VenusNotificationsInline {
	constructor () {
		this.class_name = 'notification-inline';
	}
}

/**
* Shows an inline message|error|warning|notification
* @param {string} container The element inside which the message is shown
* @param {string} text The text to shown.
* @param {int} [timeout] The interval, in seconds, after which the alert is automatically hidden
* @return {this}
*/
VenusMessagesInline.prototype.show = VenusErrorsInline.prototype.show = VenusWarningsInline.prototype.show = VenusNotificationsInline.prototype.show = function (container, text, timeout) {
	venus.alerts_inline.show(container, text, this.class_name, timeout);

	return this;
};
