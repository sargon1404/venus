/**
* Constructor for venus.controls
* @constructor
*/
function venus_controls()
{
}

venus_controls.prototype.filter = function()
{
	var self = this;

	self.filter_ajax_start();

	venus.ajax.post_form('controls-filters-form', function(response){

		venus.ajax.handle_main(response, function(){
			self.filter_ajax_end();
		}, function(){
			self.filter_ajax_end();
		});

	});
}

venus_controls.prototype.filter_reset = function()
{
	var self = this;

	self.filter_ajax_start();

	venus.ajax.post_form('controls-filters-form', function(response){

		venus.ajax.handle_main(response, function(){
			self.reset_filter_fields();
			self.filter_ajax_end();
		}, function(){
			self.filter_ajax_end();
		});

	}, null, {reset: '1'});
}

venus_controls.prototype.reset_filter_fields = function()
{
	var form_obj = venus.get('controls-filters-form');

	form_obj.find('input[type="text"]').each(function(){
		venus.get(this).val('');
	});

	form_obj.find('select').each(function(){
		venus.get(this).val(venus.get(this).find('option:first').val());
	});
}

venus_controls.prototype.filter_ajax_start = function()
{
	venus.loading.show_icon('controls-filter-loading');
	venus.get('controls-filters-action').addClass('disabled');
}

venus_controls.prototype.filter_ajax_end = function()
{
	venus.loading.hide_icon('controls-filter-loading');
	venus.get('controls-filters-action').removeClass('disabled');
}

venus_controls.prototype.order = function()
{
	var self = this;

	self.order_ajax_start();

	venus.ajax.post_form('controls-order-form', function(response){

		venus.ajax.handle_main(response, function(){
			self.order_ajax_end();
		}, function(){
			self.order_ajax_end();
		});

	});
}

venus_controls.prototype.order_reset = function()
{
	this.reset_order_fields();
	this.order();
}

venus_controls.prototype.reset_order_fields = function()
{
	var form_obj = venus.get('controls-order-form');

	form_obj.find('select').each(function(){
		venus.get(this).val(venus.get(this).find('option:first').val());
	});
}

venus_controls.prototype.order_ajax_start = function()
{
	venus.loading.show_icon('controls-order-loading');
	venus.get('controls-order-action').addClass('disabled');
}

venus_controls.prototype.order_ajax_end = function()
{
	venus.loading.hide_icon('controls-order-loading');
	venus.get('controls-order-action').removeClass('disabled');
}

venus_controls.prototype.items_per_page = function()
{
	var self = this;

	self.items_per_page_ajax_start();

	venus.ajax.post_form('controls-items-per-page-form', function(response){

		venus.ajax.handle_main(response, function(){
			self.items_per_page_ajax_end();
		}, function(){
			self.items_per_page_ajax_end();
		});

	});
}

venus_controls.prototype.items_per_page_ajax_start = function()
{
	venus.loading.show_icon('controls-items-per-page-loading');
	venus.get('controls-items-per-page-action').addClass('disabled');
}

venus_controls.prototype.items_per_page_ajax_end = function()
{
	venus.loading.hide_icon('controls-items-per-page-loading');
	venus.get('controls-items-per-page-action').removeClass('disabled');
}