/**
* Constructor for venus.tab
* @constructor
*/
function venus_tab()
{
	this.current_tab_id = 1;
}

/**
* Sets the id of the current tab
* @param {string} tab_id The id of the current tab
*/
venus_tab.prototype.set = function(tab_id)
{
	this.current_tab_id = tab_id;
}

venus_tab.prototype.auto = function()
{
	var self = this;
	jQuery(document).ready(function() {

		venus.get('tabs').children('a').each(function(){

			var id = venus.get(this).attr('id');
			var tab_id = id.replace('tab-', '');

			if(tab_id == self.current_tab_id)
			{
				venus.get('tab-' + tab_id).addClass('current');
				venus.get('tab-content-' + tab_id).show();
			}
			else
				venus.get('tab-content-' + tab_id).hide();

		});

	});
}

/**
* Switches a tab
* @param {string} tab_id The id of the tab
*/
venus_tab.prototype.switch = function(tab_id)
{
	if(tab_id == this.current_tab_id)
		return;

	venus.get('tab-' + this.current_tab_id).removeClass('current');
	venus.get('tab-' + tab_id).addClass('current');

	venus.get('tab-content-' + this.current_tab_id).hide();
	venus.get('tab-content-' + tab_id).show();

	venus.get('tab-id').val(tab_id);

	this.current_tab_id = tab_id;
}