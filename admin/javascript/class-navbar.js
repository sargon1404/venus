/**
* The Navbar Class
* @author Venus-CMS
*/
class VenusNavbar {
	constructor () {
		this.obj = null;
		this.top = null;
		this.sticky = false;
		this.confirm_strings = {};

		let self = this;

		venus.ready(function () {
			self.obj = venus.get('navbar');
			self.top = self.obj.offset().top;
		});
	}

	/**
	* Automatically stickies the navbar
	* @return {this}
	*/
	auto () {
		let self = this;

		// make the navbar sticky on scroll
		venus.ready(function () {
			jQuery(this).on('scroll', function () {
				self.toggle();
			});

			self.toggle();
		});

		return this;
	}

	/**
	* Fixates the navbar if the scrollbar is below it
	* @private
	*/
	toggle () {
		if (window.scrollY > this.top) {
			if (!this.sticky) {
				this.sticky = true;
				venus.list.close();
				this.stick();
			}
		} else {
			if (this.sticky) {
				this.sticky = false;
				venus.list.close();
				this.unstick();
			}
		}
	}

	/**
	* Fixates the navbar
	* @private
	*/
	stick () {
		this.obj.addClass('sticky');
	}

	/**
	* Unsticks the navbar
	* @private
	*/
	unstick () {
		this.obj.removeClass('sticky');
	}

	/**
	* Opens a history list
	* @param {object} element The element to which the list will be attached to
	* @param {event} event The event
	* @private
	*/
	openHistory (element, event) {
		venus.list.toggle('history-list', element, event);
	}

	/**
	* Opens a redirect list
	* @param {string} id The id of the list
	* @param {object} element The element to which the list will be attached to
	* @param {event} event The event
	* @private
	*/
	openRedirect (id, element, event) {
		venus.list.toggle(id, venus.get(element).parent(), event);
	}

	/**
	* Sets the navbar form's redirect
	* @param {string} name The redirect's name
	* @param {event} event The event
	* @private
	*/
	setRedirect (button, name, event) {
		if (event) {
			event.stopPropagation();
		}

		venus.list.close();
		venus.get(button).click();
	}

	/**
	* Submits the navbar form
	* @param {string} action The button's action [Eg: insert/update etc..]
	* @param {string} [default_action] The default action performed when the user presses enter in an input field
	* @param {bool|object} [ajax] If true, will use ajax to perform the request.
	* @param {string} [ids_name] The name of the IDS checkboxes, if any
	* @param {string|object} [error_element] The element to which the error_tooltip will be attached,if any,if no ids_name is checked
	*/
	submit (action, default_action, ajax, ids_name, error_element, event) {
		if (this.confirm_strings) {
			let strings = this.confirm_strings[action];
			if (strings) {
				this.confirm(action, default_action, ajax, ids_name, error_element, event, strings);
				return;
			}
		}

		this.submitForm(action, default_action, ajax, ids_name, error_element, event);
	}

	/**
	* Displays the confirm box if one of the navbar buttons need it
	* @private
	*/
	confirm (action, default_action, ajax, ids_name, error_element, event, strings) {
		if (ids_name && error_element) {
			if (!this.hasCheckedIds(ids_name)) {
				venus.tooltip_error.display(error_element);
				event.stopPropagation();

				return;
			}
		}

		let self = this;
		venus.confirm.open(strings.text, strings.title, function () {
			self.submitForm(action, default_action, ajax, ids_name, error_element, event);

			venus.confirm.close();
		});
	}

	/**
	* Actually submits the navbar form
	* @private
	*/
	submitForm (action, default_action, ajax, ids_name, error_element, event) {
		let form_obj = venus.get('admin-form');
		let action_obj = venus.get('admin-action');

		// check the form's validity
		if (!form_obj[0].checkValidity()) {
			venus.tab.switch(1);
			venus.get('admin-form-submit').click();

			return;
		}

		// set the action and default action
		action_obj.val(action);
		if (default_action) {
			venus.get('admin-form-action').val(default_action);
		}

		if (ids_name) {
			if (this.hasCheckedIds(ids_name)) {
				if (ajax) {
					ajax = venus.ui.get_ajax_properties(ajax);

					this.postForm(form_obj, ajax.element, ajax.on_success, ajax.on_error, ajax.extra_data);
				} else {
					form_obj.submit();
				}
			} else if (error_element) {
				venus.tooltip_error.display(error_element);
				event.stopPropagation();
			}
		} else {
			form_obj.submit();
		}
	}

	postForm (form, element, on_success, on_error, post_extra_data) {
		let self = this;

		self.postFormStart();

		venus.ajax.postForm(form, function (response) {
			// if we have an element field specified in the response, use it as element
			if (response.element) {
				element = response.element;
			}

			venus.ajax.handle(response, element, function (data) {
				if (on_success) {
					on_success(data);
				}

				venus.messages.add(data.message);
				self.post_form_end();
			}, function (data) {
				if (on_error) {
					on_error(data);
				}

				venus.errors.add(data.error);
				self.postFormEnd();
			});
		}, null, post_extra_data);
	}

	postFormStart () {
		venus.loading.show_icon('admin-navbar-buttons-loading');
		venus.get('admin-navbar-buttons-action').addClass('disabled');
	}

	postFormEnd () {
		venus.loading.hide_icon('admin-navbar-buttons-loading');
		venus.get('admin-navbar-buttons-action').removeClass('disabled');
	}

	/**
	* Returns true if there are Ids checkboxes checked
	* @private
	*/
	hasCheckedIds (ids_name) {
		let ids_checked = false;
		let objs = jQuery('input[type="checkbox"][name="' + ids_name + '[]' + '"]').each(function () {
			if (jQuery(this).prop('checked')) {
				ids_checked = true;
				return false;
			}

			return true;
		});

		return ids_checked;
	}
}
