/**
* The Ui Class
* @author Venus-CMS
*/
class VenusUi
{

	/**
	* Submits the logout form
	* @return {this}
	*/
	logout()
	{
		venus.html.submitForm('logout_form');
	}

	/**
	* Hides an element, when the close button is clicked
	* @param {string|object} element The element to hide
	* @return {this}
	*/
	close(element)
	{
		venus.get(element).hide();

		return this;
	}

	/**
	* Toggles an expand button
	* @param {string|object} expand_button The expand button
	* @param {string|object} expand_element The element which will be expanded/collapsed when expand_button is clicked
	* @param {bool} [is_small=false] If true, will apply expand-small/collapse-small classes to the expand button
	*/
	expand(expand_button, expand_element, is_small)
	{
		var button_obj = venus.get(expand_button);
		var element_obj = venus.get(expand_element);

		var expand_class = 'expand';
		var collapse_class = 'collapse';
		if(is_small)
		{
			expand_class = 'expand-small';
			collapse_class = 'collapse-small';
		}

		if(element_obj.is(':visible'))
		{
			button_obj.attr('class', expand_class);
			element_obj.hide();
		}
		else
		{
			button_obj.attr('class', collapse_class);
			element_obj.show();
		}
	}

	/**
	* Executed when a page link is clicked. Updates the content of the page with the ajax call's returned data
	* @param {string} url The url of the page
	* @param {string} update_element The element to update with the contents of the ajax call
	* @param {bool} [is_dialog] Should be set to true if the call is made from inside a dialog
	*/
	paginationUpdate(url, update_element, is_dialog)
	{
		alert("ToDo:  test pagination update");
		if(!update_element)
			update_element = venus.main_obj;

		venus.ajax.update(url, update_element, null, null, null, is_dialog);
	}

	/**
	* Executed when a page link is clicked
	* @param {string} url The url of the page
	* @param {string} update_element The element to update with the contents of the ajax call
	* @param {string} func_name The name of a function to execute when the page is changed
	*/
	paginationUpdateFunc(url, update_element, func_name)
	{
		if(!update_element)
			update_element = venus.main_obj;

		venus.loading.show(update_element);

		venus.ajax.getUrl(url, function(response){

			var fn = window[func_name];
			fn(response, update_element);

			venus.loading.hide();

		});
	}

	/**
	* Handles the pagination jump
	* @private
	*/
	paginationJump(form, page_param, max_pages)
	{
		alert("ToDo: venus.ui.paginationJump");
		var form_obj = venus.get(form);
		var page_obj = form_obj.find('input[name="venus_paginationJump"]').first();
		var page = parseInt(page_obj.val());
		if(!page || page == NaN)
			return;

		if(max_pages)
		{
			if(page > max_pages)
				return false;
		}
		if(page < 0)
			return false;

		var separator = '&';
		var action = form_obj.attr('action');

		if(action.indexOf('?') == -1)
			separator = '?';

		var url = action + separator + page_param + '=' + page;

		form_obj.attr('action', url);

		return true;
	}

	/**
	* Executed when an order link is clicked
	* @param {string} url The url of the page
	* @param {string} orderby The field to be ordered
	* @param {string} order The order direction: asc/desc
	* @param {string} update_element The element to update with the contents of the ajax call
	*/
	orderUpdate(url, orderby, order, update_element)
	{
		if(!update_element)
			update_element = venus.main_obj;

		venus.ajax.update(url, update_element);
	}

	/**
	* Executed when an order link is clicked
	* @param {string} url The url of the page
	* @param {string} orderby The field to be ordered
	* @param {string} order The order direction: asc/desc
	* @param {string} update_element The element to update with the contents of the ajax call
	* @param {string} func_name The name of a function to execute when the order link is clicked
	*/
	orderUpdateFunc(url, orderby, order, update_element, func_name)
	{
		if(!update_element)
			update_element = venus.main_obj;

		venus.loading.show(update_element);

		venus.ajax.getUrl(url, function(response){

			var fn = window[func_name];
			fn(response, update_element, orderby, order);

			venus.loading.hide();

		});
	}

}