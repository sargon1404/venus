VenusSidebar.prototype.auto = function () {
	let self = this;

	jQuery(document).ready(function () {
		self.close();
	});

	return this;
};

VenusSidebar.prototype.setHeight = function () {
};

VenusSidebar.prototype.openObj = function () {
	this.aside_obj.removeClass('closed');

	this.content_obj.show('blind');
};

VenusSidebar.prototype.closeObj = function (effect) {
	let self = this;

	if (effect) {
		this.content_obj.hide('blind', {
			complete: function () {
				self.aside_obj.addClass('closed');
			}
		});
	} else {
		this.aside_obj.addClass('closed');
	}
};
