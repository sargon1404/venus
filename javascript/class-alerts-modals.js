/**
* The Message Modal Class
* @author Venus-CMS
*/
class VenusMessageModal {
	constructor () {
		this.class_name = 'message-modal';
		this.title = venus.lang.strings.message_modal_title;
	}
}

/**
* The Error Modal Class
* @author Venus-CMS
*/
class VenusErrorModal {
	constructor () {
		this.class_name = 'error-modal';
		this.title = venus.lang.strings.error_modal_title;
	}
}

/**
* The Warning Modal Class
* @author Venus-CMS
*/
class VenusWarningModal {
	constructor () {
		this.class_name = 'warning-modal';
		this.title = venus.lang.strings.warning_modal_title;
	}
}

/**
* The Notification Modal Class
* @author Venus-CMS
*/
class VenusNotificationModal {
	constructor () {
		this.class_name = 'notification-modal';
		this.title = venus.lang.strings.notification_modal_title;
	}
}

/**
* Shows a message|error|warning|notification modal
* @param {string} text The modal's text
* @param {object} [options] The options. Supported options: {title: title, icon: icon, width: width, height: height}
* @return {this}
*/
VenusMessageModal.prototype.open = VenusErrorModal.prototype.open = VenusWarningModal.prototype.open = VenusNotificationModal.prototype.open = function (text, options) {
	options = options || {};
	options.title = options.title || this.title;

	venus.modal.open(text, this.class_name, options);

	return this;
};

/**
* The Confirm Modal
* @author Venus-CMS
*/
class VenusConfirmModal {
	constructor () {
		this.class_name = 'confirm-modal';
		this.title = venus.lang.strings.confirm_modal_title;
	}

	/**
	* Shows a confirm modal
	* @param {string} text The modal's text
	* @param {on_yes} [function] Function to be executed when the Yes button is clicked
	* @param {on_no} [function] Function to be executed when the No button is clicked
	* @param {object} [options] The options. Supported options: {title: title, icon: icon, width: width, height: height}
	* @return {this}
	*/
	open (text, on_yes, on_no, options) {
		options = options || {};
		options.title = options.title || this.title;

		on_yes = on_yes || function () { venus.modal.close(); };
		on_no = on_no || function () { venus.modal.close(); };

		let buttons = [
			{value: venus.lang.strings.yes, on_click: on_yes},
			{value: venus.lang.strings.no, on_click: on_no}
		];

		venus.modal.open(text, this.class_name, options, buttons);

		return this;
	}
}
