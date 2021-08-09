VenusAdmin.prototype.initAdmin = function () {
	this.prepareAdmin();

	this.admin = new VenusAdmin();
	this.ui = new VenusAdminUi;

	this.controls = new VenusControls;
	this.tabs = new VenusTabs();

	this.sidebar = new VenusSidebar();
	this.navbar = new VenusNavbar();

	this.image = new venus_image();
};