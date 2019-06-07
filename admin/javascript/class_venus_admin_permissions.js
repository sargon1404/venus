/**
* Constructor for the admin categories tree
* @constructor
* @param	{array} permissions Array with the defined permissions. Eg: ['perm_view','perm_add']
* @param {array} ids Array with the ids for which the permissions are defined
* @param {string} [inherit_field] The name of the inherit permission,if any
*/
function venus_admin_permissions(permissions, ids, inherit_field)
{
	this.permissions = permissions;
	this.ids = ids;
	this.inherit = inherit_field;
	this.checkboxes_array = new Array;
	this.all_checked = false;
}

/**
* Toggles the checkboxes associated with a permission
* @param {string} permission The name of the permission. Eg: perm_view
* @param {bool} checked If true will check the checkboxes.
* @private
*/
venus_admin_permissions.prototype.toggle_checkboxes = function(permission, checked)
{
	if(this.inherit)
	{
		venus.uncheck(this.inherit + '0')

		for(j = 0;j < this.ids.length;j++)
		{
			var inherit_id = this.inherit + this.ids[j];
			var obj = venus.get(inherit_id, true);
			if(!obj)
				continue;

			if(obj.checked)
			{
				obj.checked = false;
				this.enable_row(this.ids[j], false);
			}
		}
	}

	for(i = 0;i < this.ids.length;i++)
	{
		var obj = venus.get(permission + this.ids[i], true);
		if(!obj)
			continue;

		obj.checked = checked;
	}
}

/**
* @private
*/
venus_admin_permissions.prototype.toggle_all_rows = function(name)
{
	if(this.all_checked)
		this.all_checked = false;
	else
		this.all_checked = true;

	var all_ids = this.ids.concat([0]);

	for(j = 0;j < all_ids.length;j++)
	{
		var obj = venus.get(name + all_ids[j], true);
		if(obj)
			obj.checked = this.all_checked;

		if(this.inherit)
		{
			var obj = venus.get(this.inherit + all_ids[j], true);
			if(obj)
				obj.checked = false;
		}

		this.enable_row(all_ids[j], false, this.all_checked);
	}
}

/**
* @private
*/
venus_admin_permissions.prototype.toggle_all = function(obj, ugid)
{
	if(this.inherit)
	{
		var element = venus.get(this.inherit + ugid, true);
		if(element)
			element.checked = false;
	}

	this.enable_row(ugid, false, obj.checked);
}

/**
* @private
*/
venus_admin_permissions.prototype.toggle_inherit = function(check)
{
	if(!this.inherit)
		return;

	venus.html.toggle_checkboxes(this.inherit, check);

	for(j = 0;j < this.ids.length;j++)
	{
		this.enable_row(this.ids[j], check);
	}
}

/**
* @private
*/
venus_admin_permissions.prototype.toggle_inherit_row = function(obj, ugid)
{
	this.enable_row(ugid, venus.get(obj).checked);
}

/**
* @private
*/
venus_admin_permissions.prototype.set_permissions = function(obj, ugid)
{
	this.enable_row(ugid, obj.checked);
}

/**
* Enables a row of checkboxes
* @private
*/
venus_admin_permissions.prototype.enable_row = function(ugid, disabled, checked)
{
	for(i = 0;i < this.permissions.length;i++)
	{
		var element = venus.get(this.permissions[i] + ugid, true);
		if(!element)
			continue;

		element.disabled = disabled;
		if(checked != undefined)
			element.checked = checked;
	}
}