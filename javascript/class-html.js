/**
* The Html Class
* @author Venus-CMS
*/
class VenusHtml
{

	/**
	* Returns a FormData object
	* @param {string|object} form The form
	* @param {object} [data] Extra data to append to the form, if any
	* @return {FormData} The FormData object
	*/
	getForm(form, data)
	{
		var form_data = new FormData;
		if(form)
			form_data = new FormData(form[0]);

		if(data)
		{
			for(var key in data)
			{
				var val = data[key];

				form_data.append(key, val);
			}
		}

		return form_data;
	}

	/**
	* Submits a form form
	* @param {string|object} form The form to submit
	* @return {this}
	*/
	submitForm(form)
	{
		venus.get(form).submit();

		return this;
	}

	/**
	* Appends an object to a form
	* @param {string|object} form The form to append to
	* @param {object} form The object to append
	* @return {this}
	*/
	addToForm(form, obj)
	{
		venus.get(form).append(obj);

		return this;
	}

	/**
	* Creates a form hidden input field
	* @param {string} name The name of the hidden field
	* @param {string} value The value of the hidden field
	* @return {object}
	*/
	getFormHidden(name, value)
	{
		return jQuery('<input>').attr({
			type: 'hidden',
			name: name,
			value: value
		});
	}

	/**
	* Adds one/multiple hidden input fields to a form
	* If name is string and value is an array, will append multiple hidden fields with name = name[]
	* @param {string|object} form The form to append the hidden field(s) to
	* @param {string|array} names The name(s) of the hidden field to append
	* @param {string|array} value The value(s) of the hidden field to append
	* @return {this}
	*/
	addFormHidden(form, names, values)
	{
		if(typeof names == 'string' && typeof values == 'string')
			return this.addToForm(form, this.getFormHidden(names, values));

		for(var i in values)
		{
			var name = '';
			var value = values[i];

			if(typeof names == 'string')
				name = names + '[' + i + ']';
			else
				name = names[i];

			this.addToForm(form, this.getFormHidden(name, value));
		}

		return this;

	}

	/**
	* Returns true if element is checked, false otherwise
	* @param {string|object} element The element
	* @return {bool}
	*/
	isChecked(element)
	{
		return venus.get(element).prop('checked');
	}

	/**
	* Checks an element
	* @param {string|object} element The element to check
	* @return {this}
	*/
	check(element)
	{
		this.checked(element, true);

		return this;
	}

	/**
	* Unchecks element
	* @param {string|object} element The element to uncheck
	* @return {this}
	*/
	uncheck(element)
	{
		this.checked(element, false);

		return this;
	}

	/**
	* Checks/Uncheck an element
	* @param {string|object} element The element
	* @param {bool} checked The checked status
	* @return {this}
	*/
	checked(element, checked)
	{
		venus.get(element).prop(checked, checked);

		return this;
	}

	/**
	* Toggles checkboxes
	* @param {string} name The name of the checkboxes to toggle
	* @param {boolean|string|object} checked If true will check the checkboxes, if false will uncheck it. Can also be an element (string|object)
	* @return {this}
	*/
	toggle(name, checked)
	{
		name = name + '[]';

		if(typeof checked != 'boolean')
			checked = this.isChecked(checked);

		venus.getTag('input[type="checkbox"][name="' + name + '"]').each(function(){
			venus.get(this).prop('checked', checked);
		});

		return this;
	}

}