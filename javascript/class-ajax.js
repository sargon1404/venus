/**
* The Ajax Class
* @author Venus-CMS
*/
class VenusAjax
{

	/**
	* Default ajax success function
	* @param {mixed} response The response returned by the ajax call
	* @private
	*/
	onSuccess(response)
	{
		venus.loading.hide();
	}

	/**
	* Default ajax error function
	* @private
	*/
	onError(error)
	{
		if(!error || typeof error == 'object')
			error = venus.lang.strings.ajax_error;

		venus.loading.hide();

		venus.errors.add(error);
	}

	/**
	@private
	*/
	getOnSuccess(on_success)
	{
		if(on_success)
			return on_success;

		return this.onSuccess;
	}

	/**
	@private
	*/
	getOnError(on_error)
	{
		if(on_error)
			if(on_error)

		return this.onError;
	}

	/**
	* Sends an ajax request
	* @param {string} url The url
	* @param {array|object} data The data to send
	* @param {string} type The type. GET or POST
	* @param {function} [on_success] The function to be called on success
	* @param {function} [on_error] The function to be called on error
	* @return {this}
	*/
	send(url, data, type, on_success, on_error)
	{
		jQuery.ajax({
			url: url,
			data: data,
			type: type,
			success: this.getOnSuccess(on_success),
			error: this.getOnError(on_error)
		});

		return this;
	}

	/**
	* Sends an ajax GET request
	* @param {string} url The url
	* @param {function} [on_success] The function to be called on success
	* @param {function} [on_error] The function to be called on error
	* @return {this}
	*/
	getUrl(url, on_success, on_error)
	{
		return this.send(url, null, 'GET', on_success, on_error);
	}

	/**
	* Posts data to a url using ajax
	* @param {string} url The url
	* @param {array|object} data The data to send
	* @param {function} [on_success] The function to be called on success
	* @param {function} [on_error] The function to be called on error
	* @return {this}
	*/
	postUrl(url, data, on_success, on_error)
	{
		return this.send(url, data, 'POST', on_success, on_error);
	}

	/**
	* Posts a form using ajax
	* @param {string|object} The form to post. Either the id or the form object
	* @param {function} [on_success] The function to be called on success
	* @param {function} [on_error] The function to be called on error
	* @param {object} [extra_data] Extra data to send
	* @return {this}
	*/
	postForm(form, on_success, on_error, extra_data)
	{
		var form_obj = venus.get(form);
		var url = form_obj.attr('action');
		var data = venus.html.getForm(form_obj, extra_data);

		jQuery.ajax({
			url: url,
			data: data,
			type: 'POST',
			processData: false,
			contentType: false,
			success: this.getOnSuccess(on_success),
			error: this.getOnError(on_error)
		});
	}

	/**
	* Uploads to a url
	* @param {string} url The url
	* @param {array|object} The data to send
	* @param {function} [on_success] The function to be called on success
	* @param {function} [on_progress] The function to be called on progress
	* @param {function} [on_error] The function to be called on error
	* @return {this}
	*/
	uploadToUrl(url, data, on_success, on_progress, on_error)
	{
		jQuery.ajax({
			url: url,
			data: data,
			type: 'POST',
			processData: false,
			contentType: false,
			success: this.getOnSuccess(on_success),
			error: this.getOnError(on_error),
			xhr: function()
			{
				var xhr = new XMLHttpRequest;

				xhr.upload.addEventListener('progress', function(event){

					var perc = Math.round(event.loaded * 100 / event.total);

					if(on_progress)
						on_progress(perc);

				}, false);

				return xhr;
			}
		});

		return this;
	}

	/**
	* Returns the url of an ajax script
	* @param {string} name The name of the script
	* @param {array|object} data The data to send
	* @return {string} The script's url
	*/
	getScriptUrl(name, data)
	{
		var url = venus.uri.build(venus.assets.url + 'ajax.php', {ajax_name: name});

		return venus.uri.build(url, data);
	}

	/**
	* Runs a script from the ajax folder
	* @param {string} name The name of the script to run
	* @param {function} [on_success] The function to be called on success
	* @param {function} [on_error] The function to be called on error
	* @return {this}
	*/
	get(name, on_success, on_error)
	{
		var url = this.getScriptUrl(name);

		this.getUrl(url, on_success, on_error);

		return this;
	}

	/**
	* Posts data to a script from the ajax folder
	* @param {string} name The name of the script to run
	* @param {array|object} The data to send as post params
	* @param {function} [on_success] The function to be called on success
	* @param {function} [on_error] The function to be called on error
	* @return {this}
	*/
	post(name, data, on_success, on_error)
	{
		var url = this.getScriptUrl(name);

		this.postUrl(url, data, on_success, on_error);

		return this;
	}

	/**
	* Uploads to a script in the ajax folder
	* @param {string} name The name of the script
	* @param {array|object} The data to send
	* @param {function} [on_success] The function to be called on success
	* @param {function} [on_progress] The function to be called on progress
	* @param {function} [on_error] The function to be called on error
	* @return {this}
	*/
	upload(name, data, on_success, on_progress, on_error)
	{
		var url = this.getScriptUrl(name);

		return this.uploadToUrl(url, data, on_success, on_progress, on_error);
	}

	/**
	* Calls url and updates the innerHTML of element with the output returned by the ajax call
	* @param {string} url The url to call
	* @param {string|object} element Either the element's id or an object. If not specified venus.main_obj is used
	* @param {array|object} The data to send
	* @param {function} [on_success] The function to be called on success
	* @param {function} [on_error] The function to be called on error
	* @param {bool} [resize_dialog] If true, will call venus.dialog.resize. Usefull, if the call is made from inside a dialog
	* @param {method} [method] The method: GET|POST
	* @return {this}
	*/
	update(url, element, data, on_success, on_error, resize_dialog, method)
	{
		if(!element)
			element = venus.main_obj;
		if(!method)
			method = 'GET';

		venus.loading.show(element);

		var self = this;
		this.send(url, data, method ,function(response){

			self.handle(response, element, on_success, on_error);

			if(resize_dialog)
				venus.dialog.resize();

			venus.loading.hide();

		});

		return this;
	}

	/**
	* Alias of update. Will update the venus.content_obj element
	*/
	updateContent(url, data, on_success, on_error, resize_dialog)
	{
		this.update(url, venus.content_obj, data, on_success, on_error, resize_dialog);

		return this;
	}

	/**
	* Alias of update. Will update the venus.main_obj element
	*/
	updateMain(url, element, data, on_success, on_error, resize_dialog)
	{
		this.update(url, venus.main_obj, data, on_success, on_error, resize_dialog);

		return this;
	}

	/**
	* Calls url and updates the innerHTML of element with the output returned by the ajax call
	* @param {string} url The url to call
	* @param {string|object} element Either the element's id or an object
	* @param {array|object} The data to send
	* @param {function} [on_success] The function to be called on success
	* @param {function} [on_error] The function to be called on error
	* @param {bool} resize_dialog If true, will call venus.dialog.resize. Usefull, if the call is made from inside a dialog
	* @return {this}
	*/
	updatePost(url, element, data, on_success, on_error, resize_dialog)
	{
		this.update(url, element, data, on_success, on_error, resize_dialog, 'POST');

		return this;
	}

	/**
	* Handles an ajax returned response
	* @param {string} response The response returned by an ajax call
	* @param {string|object} [element] The element which will be updated by the call's html
	* @param {function} [on_success] The function to be called on success
	* @param {function} [on_error] The function to be called on error
	* @param {function} [on_before_update] Function to be called before the html content is updated
	* @param {function} [on_after_update] Function to be called after the html content is updated
	*/
	handle(response, element, on_success, on_error, on_before_update, on_after_update)
	{
		var html = '';

		try
		{
			if(venus.debug)
				venus.log(response, 'Ajax Response');

			var data = venus.decode(response);

			if(venus.debug)
				venus.log(data, 'Ajax Response Data');

			if(data.ok)
			{
				html = data.html;

				if(element)
				{
					if(on_before_update)
						on_before_update(data);

					if(html)
					{
						venus.get(element).html(html);

						//init the tooltips/modals over element
						venus.initHtml(element);
					}

					if(on_after_update)
						on_after_update(data);

					if(on_success)
						on_success(data);
				}
			}
			else
			{
				if(on_error)
					on_error(data);
			}
		}
		catch(err)
		{
			//probably invalid json code
			if(venus.debug)
				venus.log(err, 'Invalid json code');

			this.on_error(venus.lang.strings.ajax_json_error);
		}
	}

	/**
	* Alias for handle. Will use the content element as element
	*/
	handleContent(response, on_success, on_error, on_before_update, on_after_update)
	{
		this.handle(response, venus.content_obj, on_success, on_error, on_before_update, on_after_update);
	}

	/**
	* Alias for handle. Will use the main element as element
	*/
	handleMain(response, on_success, on_error, on_before_update, on_after_update)
	{
		this.handle(response, venus.main_obj, on_success, on_error, on_before_update, on_after_update);
	}

	/**
	* Handles an ajax alert
	* @param {string} response The response returned by an ajax call
	*/
	handleAlert(response)
	{
		var data = venus.decode(response);

		if(data.ok)
		{
			if(data.message)
				venus.messages.add(data.message);
			else if(data.warning)
				venus.messages.add(data.warning);
			else if(data.notification)
				venus.messages.add(data.notification);
		}
		else
			venus.errors.add(data.error);
	}

}