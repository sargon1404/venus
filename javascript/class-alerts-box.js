/**
* The Alerts Classes. Messages|Errors|Warnings|Notifications
* Will show the message in the right-bottom corner
* @author Venus-CMS
*/
class VenusAlerts {
	/**
	* @property {int} timeout The default timeout
	* @property {int} opened The number of currently opened alerts
	*/
	constructor () {
		this.obj = null;
		this.opened = 0;
		this.timeout = 8;

		this.messages = new VenusMessages();
		this.errors = new VenusErrors();
		this.warnings = new VenusWarnings();
		this.notifications = new VenusNotifications();
	}

	/**
	* Inits the alerts
	* @private
	*/
	init () {
		if (this.obj) {
			return;
		}

		jQuery('body').append('<div id="alerts-box"></div>');

		this.obj = venus.get('alerts-box');
		this.obj.hide();
	}

	/**
	* Adds an alert
	* @param {string} text The text of the alert
	* @param {string} class_name The alert's class
	* @param {int} [timeout] The interval, in seconds, after which the alert is automatically hidden
	* @return {this}
	*/
	add (text, class_name, timeout) {
		this.init();

		let id = venus.generateId('alert');

		timeout = timeout || this.timeout;
		class_name = class_name || 'alert-box';

		let html = '\
			<div id="' + id + '" class="' + class_name + '">\
				<a href="javascript:void(0)" onclick="venus.alerts.close(\'' + id + '\')" class="close"></a>\
				<div class="alert-box-text">' + text + '</div>\
				<div class="clear"></div>\
			</div>';

		this.obj.prepend(html);

		let obj = venus.get(id);
		obj.hide();

		// hide the alert after timeout seconds
		if (timeout) {
			setTimeout(function () {
				venus.alerts.close(id);
			}, timeout * 1000);
		}

		this.show(obj);

		return this;
	}

	/**
	* @private
	*/
	show (obj) {
		if (!this.opened) {
			this.obj.show();
		}

		this.opened++;

		this.showObj(obj);
	}

	/**
	* @private
	*/
	showObj (obj) {
		obj.show();
	}

	/**
	* Closes an alert
	* @param {string} id The id of the alert to close
	*/
	close (id) {
		if (!venus.exists(id)) {
			return;
		}

		this.opened--;

		this.hideObj(venus.get(id), this.opened);
	}

	/**
	* @private
	*/
	hideObj (obj, opened) {
		obj.hide();
		obj.remove();

		if (!opened) {
			this.obj.hide();
		}
	}
}

/**
* The Messages class
* @author Venus-CMS
*/
class VenusMessages {
	constructor () {
		this.class_name = 'message-box';
	}
}

/**
* The Errors class
* @author Venus-CMS
*/
class VenusErrors {
	constructor () {
		this.class_name = 'error-box';
	}
}

/**
* The Warnings class
* @author Venus-CMS
*/
class VenusWarnings {
	constructor () {
		this.class_name = 'warning-box';
	}
}

/**
* The Notifications class
* @author Venus-CMS
*/
class VenusNotifications {
	constructor () {
		this.class_name = 'notification-box';
	}
}

/**
* Shows a message|error|warning|notification
* @param {string} text The text to shown.
* @param {int} [timeout] The interval, in seconds, after which the alert is automatically hidden
* @return {this}
*/
VenusMessages.prototype.add = VenusErrors.prototype.add = VenusWarnings.prototype.add = VenusNotifications.prototype.add = function (text, timeout) {
	venus.alerts.add(text, this.class_name, timeout);

	return this;
};
