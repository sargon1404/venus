if(jQuery.ui)
{
	VenusDialog.prototype.showObj = function()
	{
		this.dialog.overlay.show();
		this.dialog.obj.show('scale');
	}

	VenusDialog.prototype.hideObj = function()
	{
		var self = this;
		this.dialog.obj.hide('scale', {}, function(){
			self.dialog.overlay.hide();
		});
	}

	VenusModal.prototype.showObj = function()
	{
		this.overlay_obj.show();
		this.obj.show('slide');
	}

	VenusModal.prototype.hideObj = function()
	{
		var self = this;
		this.obj.hide('slide', {}, function(){
			self.overlay_obj.hide();
		});
	}

	VenusAlerts.prototype.showObj = function(obj)
	{
		obj.show('drop', {direction: 'up'});
	}

	VenusAlerts.prototype.hideObj = function(obj, opened)
	{
		var self = this;
		obj.hide('drop', {direction: 'down'}, function(){
			obj.remove();
			if(!opened)
				self.alertsObj.hide();
		});
	}

	VenusMenu.prototype.toggleObj = function(obj)
	{
		obj.toggle('blind');
	}

	VenusMenu.prototype.toggleChildObj = function(obj)
	{
		obj.toggle('blind');
	}

	VenusList.prototype.showObj = function()
	{
		this.obj.show('blind');
	}

	VenusList.prototype.hideObj = function()
	{
		this.obj.hide('blind');
	}

}