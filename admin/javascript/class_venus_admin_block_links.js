/**
* Constructor for venus.block_links
* @constructor
*/
function venus_block_links()
{
	this.min_chars = 3;
}

/**
* Sets the url and the link
*/
venus_block_links.prototype.set = function (url, link)
{
	if(!link)
		link = url;

	venus.set_value('url', url);
	venus.set_value('url_real', link);
}

/**
* Sets the url and the link
*/
venus_block_links.prototype.set_comments = function (title, id2, id3)
{
}

/**
* Sets the url and the link by specifying an url and a title for the link
*/
venus_block_links.prototype.set_link = function (url, title)
{
	var link = '<a href="' + url + '">' + title + '</a>';

	this.set(url, link);
}

/**
* Gets the url of a link and inserts it in the editor
* @param {int}	bid The block_links's id
* @param {int}	id2 The block's id2 param
* @param {int}	id3 The block's id3 param
* @param {function} Callback function to be called after the url is generated. If none is specified,the url is updated with the result generated
*/
venus_block_links.prototype.get_url = function (bid, id2, id3, callback_function)
{
	if(!id2)
		id2 = 0;
	if(!id3)
		id2 = 3;

	var params = {id:bid, id2:id2, id3:id3};

	var self = this;

	venus.ajax.get('get_block_url', params, function(data){

		data = venus.decode(data);

		if(callback_function)
			callback_function(data);
		else
		{
			var url = data[0];
			var link = data[1];

			if(!link)
				link = '<a href="' + url + '">' + url + '</a>';

			self.set(url, link);
		}

	});
}


/**
* Gets the data
* @param {int}	bid The block's id
* @param {string} value The value to filter the data on
* @param {function} Callback function to be called after the data is retrieved
*/
venus_block_links.prototype.get_data = function (bid, value, callback_function)
{
	var params = {bid: bid, value: value};

	venus.ajax.get('get_block_links_data', params, function(data){
		callback_function(data);
	});
}

/**
* Returns the populate data of a block
* @param {int}	bid The block's id
* @param {string} value The value to filter the data on
* @param {string} element The element to attach to data to
* @param {bool} [check_length=true] If true,will only show the populate popup if the element to which it's attached has more than min_chars characters.
*/
venus_block_links.prototype.populate_with_data = function (bid, value, element, check_length, is_comments_area)
{
	if(check_length == undefined)
		check_length = true;
	if(is_comments_area == undefined)
		is_comments_area = 0;
	else if(is_comments_area)
		is_comments_area = 1;
	else
		is_comments_area = 0;

	if(check_length)
	{
		if(value.length < this.min_chars)
			return;
	}

	var params = {bid: bid, value: value, is_comments_area : is_comments_area};

	venus.ajax.get('get_block_links_data', params, function(data){

		venus.populate.display(element, data);
		venus.dialog.resize(venus.populate.populate_obj.offsetHeight);

	});
}

/**
* Function to be executed when a search input is used
* @param {int} bid The block's id
* @param {string} element The element to show the populate data next to and whose value is used to filter the data
*/
venus_block_links.prototype.search = function(bid, element)
{
	var value = venus.get_value(element);

	venus.block_links.populate_with_data(bid, value, element);
}