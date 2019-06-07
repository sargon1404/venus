/**
* The Admin Class
* @author Venus-CMS
*/
class VenusAdmin extends Venus
{

	/**
	* @private
	*/
	prepareAdmin()
	{
		this.aside_obj = null;

		var self = this;
		venus.ready(function(){

			self.aside_obj = self.getTag('aside');

		});
	}

	/**
	* Opens the upload popup
	* @param {string|object} element The element to display in the popup
	* @param {string} [title] The popup's title
	* @return {this}
	*/
	openUploadPopup(element, title)
	{
		venus.dialog.openElement(element, {width: '600px'});

		//this.initUploadPopup(element);

		return this;
	}

	initUploadPopup(element)
	{
		if(this.is_init_upload_widget)
			return;

		this.is_init_upload_widget = true;

		this.uploader.init(element, {accept : 'application/.*zip'}, function(response){

			venus.ajax.handle(response, venus.main_obj, function(data){
				//on success
				venus.messages.add(data.message);
				venus.widget.close(element);

			}, function(data){
				//on error
				venus.errors_inline.display('upload-form-error', data.error);
			}, function(){
				//on before update
				//venus.loading.show_over_main();
			}, function(){
				//on after update
				//venus.loading.hide();
			});

		}, function(){
			venus.errors_inline.display('upload-form-error', venus.strings['extension_upload_error']);
		});
	}


	/**
	* Updates the value of element with the SEO safe version of slug
	* @param {string} slug The url slug
	* @param {string|object} element Either the element's id or an object
	*/
	updateSeo(slug, element)
	{
		slug = this.trim(slug);
		slug = slug.toLowerCase();
		slug = slug.replace(/ /g, '-');
		slug = slug.replace(/_/g, '-');
		slug = slug.replace(/[^a-z0-9\-]/g, '');
		slug = slug.replace(/\-+/g, '-');
		slug = encodeURIComponent(slug);

		this.get(element).val(slug);
	}

}