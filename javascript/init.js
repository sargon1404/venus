Venus.prototype.init = function() {
	this.prepare();

	this.loading = new VenusLoading;
	this.ajax = new VenusAjax;
	this.uri = new VenusUri;
	this.input= new VenusInput;
	this.html = new VenusHtml;
	this.theme = new VenusTheme;
	this.ui = new VenusUi;

	this.dialog = new VenusDialog;
	this.popup = new VenusPopup;
	this.tooltips = new VenusTooltips;
	this.tooltip = new VenusTooltip;

	this.modals = new VenusModals;
	this.modal = new VenusModal;
	this.message = this.modal.message;
	this.error = this.modal.error;
	this.warning = this.modal.warning;
	this.notification = this.modal.notification;
	this.confirm = this.modal.confirm;

	this.alerts = new VenusAlerts;
	this.messages = this.alerts.messages;
	this.errors = this.alerts.errors;
	this.warnings = this.alerts.warnings;
	this.notifications = this.alerts.notifications;

	this.alerts_inline = new VenusAlertsInline;
	this.messages_inline = this.alerts_inline.messages;
	this.errors_inline = this.alerts_inline.errors;
	this.warnings_inline = this.alerts_inline.warnings;
	this.notifications_inline = this.alerts_inline.notifications;

	this.populate = new VenusPopulate;
	this.list = new VenusList;

	this.controls = new VenusControls;
	this.tabs = new VenusTabs;




	//this.iframe = new venus_iframe;
	//this.editor = new venus_editor;
	//this.drag = new venus_drag;
	//this.drop = new venus_drop;



	//this.media = new venus_media;
	//this.uploader = new venus_uploader;
	//this.bbcode_editor = new venus_bbcode_editor;



	//this.rating = new venus_rating;
	//this.comments = new venus_comments;
}


let venus = new Venus;