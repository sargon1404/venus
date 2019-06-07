VenusAdmin.prototype.initAdmin = function()
{
	jQuery.noConflict();

	this.prepareAdmin();

	this.admin = new VenusAdmin;
	this.sidebar = new VenusSidebar;
	this.navbar = new VenusNavbar;






	this.tab = new venus_tab;
	this.controls = new venus_controls;



	this.image = new venus_image;

	this.ui.init();
}

var venus = new VenusAdmin;