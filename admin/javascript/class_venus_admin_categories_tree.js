/**
* Constructor for the admin categories tree
* @constructor
*/
function venus_admin_categories_tree(all_categories_name, change_name)
{
	this.change_name = change_name;
	this.all_categories_name = all_categories_name;
}

/**
* Toggles all the buttons based on checked
* @private
*/
venus_admin_categories_tree.prototype.toggle_all = function(checked)
{
	venus.html.toggle_checkboxes('categories', checked);
	if(this.change_name)
		venus.check(this.change_name + '0');
}

/**
* @private
*/
venus_admin_categories_tree.prototype.select_item = function(subcategories, item)
{
	if(this.all_categories_name)
		venus.uncheck(this.all_categories_name);

	if(this.change_name)
		venus.check(this.change_name + '0');

	if(subcategories)
		this.toggle('categories', subcategories, item.checked);
}


/**
* Toggles the checkboxes
* @param {string} name The name of the checkboxes
* @param {array} ids The ids to toggle
* @param {bool} checked If true will check the checkboxes, if false will uncheck it
* @return {this}
*/
venus_admin_categories_tree.prototype.toggle = function(name, ids, checked)
{
	for(var i = 0; i < ids.length; i++)
	{
		venus.html.checked(name + ids[i], checked);
	}

	return this;
}