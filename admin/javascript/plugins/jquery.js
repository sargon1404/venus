VenusUi.prototype.close = function(container)
{
	venus.get(container).fadeOut();
}

VenusAlerts.prototype.showObj = function(obj)
{
	obj.fadeIn();
}

VenusAlerts.prototype.hideObj = function(obj, opened)
{
	var self = this;
	obj.fadeOut(400, function(){
		obj.remove();
		if(!opened)
			self.obj.hide();
	});
}

VenusAlertsInline.prototype.showObj = function(obj)
{
	obj.fadeIn();
}

VenusAlertsInline.prototype.hideObj = function(obj, parent)
{
	parent.fadeOut(400, function(){
		obj.remove();
	});
}

VenusPopup.prototype.showObj = function(obj)
{
	obj.fadeIn();
}

VenusPopup.prototype.hideObj = function(obj, opened)
{
	var self = this;
	obj.fadeOut(400, function(){
		if(!opened)
			self.obj.hide();
	});
}