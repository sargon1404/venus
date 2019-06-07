/**
* Constructor for the image
* @constructor
*/
function venus_image()
{
}

/**
* Inits an image for drag&drop + upload
* @param {string} element The image container element
* @param {object} {data} Data to be passed. Contains: width,height,token_field,token_value
*/
venus_image.prototype.init = function(element, data)
{
	$ = jQuery;

	this.element = venus.get(element);
	this.process_element = venus.get(data.process_id);
	this.progress_element = venus.get(data.progress_id);
	this.file_element = venus.get(data.file_id);
	this.data = data;

	var self = this;

	this.element.mouseover(function(){
		self.element.addClass('image-over');
	});

	this.element.mouseout(function(){
		self.element.removeClass('image-over');
	});

	this.file_element.click(function(event){ event.stopPropagation();});
	this.file_element.change(function(){self.upload_file();});

	this.element.click(function(){
		self.file_element.click();
	});

	this.element.each(function(){

		$(this).data('self', self);

		$(this).on('drop', self.drop);
		$(this).on('dragenter', self.drag_enter);
		$(this).on('dragover', self.drag_over);
		$(this).on('dragleave', self.drag_leave);

	});
}

venus_image.prototype.drag_enter = function()
{
	$ = jQuery;

	$(this).data('self').element.addClass('image-over');

	return false;
}

venus_image.prototype.drag_leave = function()
{
	$ = jQuery;

	$(this).data('self').element.removeClass('image-over');

	return false;
}

venus_image.prototype.drag_over = function()
{
	return false;
}

venus_image.prototype.drop = function(event)
{
	$ = jQuery;

	event.preventDefault();

	var self = $(this).data('self');

	var data = {
		image: event.originalEvent.dataTransfer.files[0],
		width: self.data.width,
		height: self.data.height,
		process: self.process_element.val()
	};
	data[self.data.token_field] = self.data.token;

	var form = venus.html.get_form(null, data);

	self.upload(form);
}

venus_image.prototype.upload_file = function(file_element)
{
	var self = this;
	var data = {
		width: self.data.width,
		height: self.data.height,
		process: self.process_element.val()
	};
	data[self.data.token_field] = self.data.token;

	var form = venus.html.get_form(null, data);
	form.append('image', self.file_element[0].files[0], self.file_element.val());

	self.upload(form);
}

/**
* Uploads the image
*/
venus_image.prototype.upload = function(data)
{
	var self = this;

	self.progress_element.val(0);
	self.progress_element.show();
	venus.loading.show_small();

	venus.ajax.upload('upload_image', data, function(data){

		data = venus.decode(data);
		if(data.error)
		{
			venus.error(data.error);
			return;
		}

		self.element.html(data.html);
		self.progress_element.hide();
		self.element.removeClass('image-over');
		venus.loading.hide_small();

	}, function(perc){

		self.progress_element.val(perc);

	});
}

/**
* Deletes the image
*/
venus_image.prototype.clear = function(element)
{
}