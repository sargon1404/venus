VenusUi.prototype.init = function()
{
	this.confirm_strings = {};
	this.form_action_ajax = {};
}

/**
* Submits the multi form in the admin
*/
VenusUi.prototype.submit_multi_form = function(url)
{
	var form = venus.get('admin-multi-form');
	form.attr('action', url);

	form.submit();
}

/**
* Submits the multi ajax form in the admin
*/
VenusUi.prototype.submit_multi_ajax_form = function(url)
{
	var form = venus.get('admin-multi-ajax-form');
	form.attr('action', url);

	form.submit();
}

/**
* Sets the confirm strings used in the navbar/quick action/form action
*/
VenusUi.prototype.set_confirm_strings = function(strings)
{
	for(var i in strings)
	{
		var str = strings[i];
		if(typeof str.type === 'string' )
			str.type = [str.type];

		for(var j = 0; j < str.type.length; j++)
		{
			var type = str.type[j];

			if(!this.confirm_strings[type])
				this.confirm_strings[type] = {};

			this.confirm_strings[type][str.action] = {title: str.title, text: str.text};
		}
	}

	if(this.confirm_strings['navbar'])
		venus.navbar.confirm_strings = this.confirm_strings['navbar'];
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
VenusUi.prototype.post_form = function(item_id, form, element, disable_element, on_success, on_error, post_extra_data)
{
	var self = this;

	self.post_form_start(item_id, disable_element);

	venus.ajax.post_form(form, function(response){

		//if we have an element field specified in the response, use it as element
		if(response.element)
			element = response.element;

		venus.ajax.handle(response, element, function(data){

			if(on_success)
				on_success(data);

			venus.messages.add(data.message);
			self.post_form_end(item_id, disable_element);

		}, function(data){

			if(on_error)
				on_error(data);

			venus.errors.add(data.error);
			self.post_form_end(item_id, disable_element);

		});
	}, null, post_extra_data);
}

VenusUi.prototype.post_form_start = function(item_id, disable_element)
{
	venus.tooltip.hide();
	venus.loading.show_icon('loading-item-' + item_id);
	venus.get(disable_element).addClass('disabled');
}

VenusUi.prototype.post_form_end = function(item_id, disable_element)
{
	venus.loading.hide_icon('loading-item-' + item_id);
	venus.get(disable_element).removeClass('disabled');
}

/**
* Displays the confirm box if one of the form actions need it; uses ajax, if marked, to make the call or submits the form otherwise
* @private
*/
VenusUi.prototype.form_action = function(item_id, form_id, select_id)
{
	var self = this;
	var select_obj = venus.get(select_id);
	var action = select_obj.val();
	var disable_element = 'item-form-' + item_id;
	var use_ajax = parseInt(select_obj.find('option:selected').first().attr('data-ajax'));
	var ajax = null;
	var form = venus.get(form_id);

	if(use_ajax)
	{
		//check if we're having ajax options for this action
		var ajax_key = item_id + '-' + action;
		if(this.form_action_ajax[ajax_key])
			ajax = this.get_ajax_properties(this.form_action_ajax[ajax_key]);
		else
			ajax = this.get_default_ajax_properties();
	}

	if(this.confirm_strings['form'])
	{
		var strings = this.confirm_strings['form'][action];
		if(strings)
		{
			var ret = false;

			venus.confirm.open(strings.text, strings.title, function(){

				if(ajax)
					self.post_form(item_id, form_id, ajax.element, disable_element, ajax.on_success, ajax.on_error, ajax.extra_data);
				else
				{
					//remove the onsubmit event
					form.attr('onsubmit', '');

					venus.html.submit_form(form);
				}

				venus.confirm.close();

			});

			return false;
		}
	}

	if(ajax)
		this.post_form(item_id, form_id, ajax.element, disable_element, ajax.on_success, ajax.on_error, ajax.extra_data);
	else
		return true;

	return false;
}

/**
* Displays the confirm box if one of the quick action links need it; uses ajax, if marked, to make the call or submits the form otherwise
* @private
*/
VenusUi.prototype.quick_action = function(item_id, action, ajax, use_redirect, obj)
{
	var self = this;

	if(this.confirm_strings['quick'])
	{
		var strings = this.confirm_strings['quick'][action];
		if(strings)
		{
			venus.confirm.open(strings.text, strings.title, function(){

				self.handle_quick_action(item_id, action, ajax, use_redirect, obj);

				venus.confirm.close();

			});

			return false;
		}
	}

	this.handle_quick_action(item_id, action, ajax, use_redirect, obj);

	return false;
}

VenusUi.prototype.handle_quick_action = function(item_id, action, ajax, use_redirect, obj)
{
	var url = venus.get(obj).attr('href');

	if(use_redirect)
	{
		venus.redirect(url);
		return;
	}

	if(ajax)
	{
		ajax = this.get_ajax_properties(ajax);

		this.post_quick_action(item_id, url, ajax.element, 'item-quick-action-' + item_id, ajax.on_success, ajax.on_error);
	}
	else
		this.submit_multi_form(url);
}

/**
* Returns the default ajax properties
*/
VenusUi.prototype.get_default_ajax_properties = function()
{
	var data = {element: venus.main_obj, on_success: null, on_error: null, extra_data: {}};
	data.extra_data[venus.config.response_param] = 'ajax';

	return data;
}

/**
* Returns the available ajax properties available for quick actions/form actions
*/
VenusUi.prototype.get_ajax_properties = function(ajax)
{
	var props = this.get_default_ajax_properties();
	jQuery.extend(props, ajax);

	props.element = venus.get(props.element);

	return props;
}

/**
* Posts the quick action form using ajax
*/
VenusUi.prototype.post_quick_action = function(item_id, url, element, disable_element, on_success, on_error)
{
	var form = venus.get('admin-multi-ajax-form');
	form.attr('action', url);

	this.post_form(item_id, form, element, disable_element, on_success, on_error);
}


VenusUi.prototype.order_update = function(url, orderby, order, update_element, func_name)
{
	venus.ajax.update(url, update_element, {}, function(){

		if(func_name)
			func_name(response, url, orderby, order);

		venus.get('orderby').val(orderby);
		venus.get('order').val(order);
	});
}

VenusUi.prototype.update_status_1 = function()
{
	venus.get('status').val(1);
}

VenusUi.prototype.update_status_0 = function()
{
	venus.get('status').val(0);
}