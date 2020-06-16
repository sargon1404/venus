/**
* The Admin Class
* @author Venus-CMS
*/
class VenusAdminUi extends VenusUi {
	constructor () {
		super();

		this.confirm_strings = {};
		this.form_action_ajax = {};
	}

	/**
	* Submits the multi form in the admin
	*/
	submitMultiForm (url) {
		let form = venus.get('admin-multi-form');
		form.attr('action', url);

		form.submit();
	}

	/**
	* Submits the multi ajax form in the admin
	* @param {string} url The url to submnit the form to
	*/
	submitMultiAjaxForm (url) {
		let form = venus.get('admin-multi-ajax-form');
		form.attr('action', url);

		form.submit();
	}

	/**
	* Sets the confirm strings used in the navbar/actions list/actions form
	* @param {array} strings The strings
	*/
	setConfirmStrings (strings) {
		for (let i in strings) {
			let str = strings[i];
			if (typeof str.type === 'string') {
				str.type = [str.type];
			}

			for (let j = 0; j < str.type.length; j++) {
				let type = str.type[j];

				if (!this.confirm_strings[type]) {
					this.confirm_strings[type] = {};
				}

				this.confirm_strings[type][str.action] = {title: str.title, text: str.text};
			}
		}

		if (this.confirm_strings.navbar) {
			venus.navbar.confirm_strings = this.confirm_strings.navbar;
		}
	}

	/**
	* Posts a form using an ajax request and updates the html
	* @param {string} item_id The id of the item for which the form is posted
	* @param {string|object} form The form to be posted
	* @param {string|object} element The element to be updated with the html returned by the post request
	* @param {string|object} disable_element The element which will be disabled before the ajax call is completed
	* @param {function} on_success Function to be executed on success, if any
	* @param {function} on_success Function to be executed on error, if any
	* @param {object} post_extra_data Extra post data
	*/
	postForm (item_id, form, element, disable_element, on_success, on_error, post_extra_data) {
		let self = this;

		self.postFormStart(item_id, disable_element);

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
				self.postFormEnd(item_id, disable_element);
			}, function (data) {
				if (on_error) {
					on_error(data);
				}

				venus.errors.add(data.error);
				self.postFormEnd(item_id, disable_element);
			});
		}, null, post_extra_data);
	}

	/**
	* @private
	*/
	postFormStart (item_id, disable_element) {
		venus.tooltip.hide();
		venus.loading.showIcon('loading-item-' + item_id);
		venus.get(disable_element).addClass('disabled');
	}

	/**
	* @private
	*/
	postFormEnd (item_id, disable_element) {
		venus.loading.hideIcon('loading-item-' + item_id);
		venus.get(disable_element).removeClass('disabled');
	}

	/**
	* Displays the confirm modal if one of the form actions need it; uses ajax, if marked, to make the call or submits the form otherwise
	* @private
	*/
	formAction (item_id, form_id, select_id) {
		let self = this;
		let select_obj = venus.get(select_id);
		let action = select_obj.val();
		let disable_element = 'item-form-' + item_id;
		let use_ajax = parseInt(select_obj.find('option:selected').first().attr('data-ajax'));
		let ajax = null;
		let form = venus.get(form_id);

		if (use_ajax) {
			// check if we're having ajax options for this action
			let ajax_key = item_id + '-' + action;
			if (this.form_action_ajax[ajax_key]) {
				ajax = this.getAjaxProperties(this.form_action_ajax[ajax_key]);
			} else {
				ajax = this.getDefaultAjaxProperties();
			}
		}

		if (this.confirm_strings.form) {
			let strings = this.confirm_strings.form[action];
			if (strings) {
				let ret = false;

				venus.confirm.open(strings.text, strings.title, function () {
					if (ajax) {
						self.postForm(item_id, form_id, ajax.element, disable_element, ajax.on_success, ajax.on_error, ajax.extra_data);
					} else {
						// remove the onsubmit event
						form.attr('onsubmit', '');

						venus.html.submit_form(form);
					}

					venus.confirm.close();
				});

				return false;
			}
		}

		if (ajax) {
			this.postForm(item_id, form_id, ajax.element, disable_element, ajax.on_success, ajax.on_error, ajax.extra_data);
		} else {
			return true;
		}

		return false;
	}

	/**
	* Displays the confirm box if one of the actions list links need it; uses ajax, if marked, to make the call or submits the form otherwise
	* @private
	*/
	doListAction (item_id, action, ajax, use_redirect, obj) {
		let self = this;

		if (this.confirm_strings.quick) {
			let strings = this.confirm_strings.quick[action];
			if (strings) {
				venus.confirm.open(strings.text, strings.title, function () {
					self.handleListAction(item_id, action, ajax, use_redirect, obj);

					venus.confirm.close();
				});

				return false;
			}
		}

		this.handleListAction(item_id, action, ajax, use_redirect, obj);

		return false;
	}

	/**
	* @private
	*/
	handleListAction (item_id, action, ajax, use_redirect, obj) {
		let url = venus.get(obj).attr('href');

		if (use_redirect) {
			venus.redirect(url);
			return;
		}

		if (ajax) {
			ajax = this.getAjaxProperties(ajax);

			this.postListAction(item_id, url, ajax.element, 'item-actions-list-' + item_id, ajax.on_success, ajax.on_error);
		} else {
			this.submitMultiForm(url);
		}
	}

	/**
	* Returns the default ajax properties
	* @private
	*/
	getDefaultAjaxProperties () {
		let data = {element: venus.main_obj, on_success: null, on_error: null, extra_data: {}};
		data.extra_data[venus.config.response_param] = 'ajax';

		return data;
	}

	/**
	* Returns the available ajax properties available for actions list/actions forms
	* @private
	*/
	getAjaxProperties (ajax) {
		let props = this.getDefaultAjaxProperties();
		jQuery.extend(props, ajax);

		props.element = venus.get(props.element);

		return props;
	}

	/**
	* Posts the list action form using ajax
	* @private
	*/
	postListAction (item_id, url, element, disable_element, on_success, on_error) {
		let form = venus.get('admin-multi-ajax-form');
		form.attr('action', url);

		this.postForm(item_id, form, element, disable_element, on_success, on_error);
	}

	/**
	* @private
	*/
	orderUpdate (url, orderby, order, update_element, func_name) {
		venus.ajax.update(url, update_element, {}, function () {
			if (func_name) {
				func_name(response, url, orderby, order);
			}

			venus.get('orderby').val(orderby);
			venus.get('order').val(order);
		});
	}

	/**
	* @private
	*/
	updateStatus1 () {
		venus.get('status').val(1);
	}

	/**
	* @private
	*/
	updateStatus0 () {
		venus.get('status').val(0);
	}
}
