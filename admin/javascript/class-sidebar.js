/**
* The Sidebar Class
* @author Venus-CMS
*/
class VenusSidebar
{

	constructor()
	{
		this.main_obj = null;
		this.aside_obj = null;
		this.top_obj = null;
		this.content_obj = null;
		this.cookie_name = 'venus-sidebar-closed';

		var self = this;

		venus.ready(function() {

			self.main_obj = venus.main_obj;
			self.aside_obj = venus.aside_obj;
			self.top_obj = venus.get('sidebar-top');
			self.content_obj = venus.get('sidebar-content');

			self.top_obj.click(function(){
				self.toggle();
			});

		});
	}

	/**
	* Automatically opens/closes the sidebar based on the sidebar cookie
	* @return {this}
	*/
	auto()
	{
		var self = this;

		venus.ready(function() {

			if(venus.input.getCookie(self.cookie_name))
			{
				//we have the close cookie set; auto close the sidebar
				self.setHeight();
				self.close();

				return;
			}

		});

		return this;
	}

	/**
	* Sets the height of the sidebar
	* @return {this}
	*/
	setHeight()
	{
		var top_height = this.top_obj.outerHeight();
		var content_height = this.content_obj.outerHeight();

		this.aside_obj.height(top_height + content_height);

		return this;
	}

	/**
	* Toggles the sidebar open/close
	* @return {this}
	*/
	toggle()
	{
		if(this.aside_obj.hasClass('closed'))
			this.open();
		else
			this.close(true);

		return this;
	}

	/**
	* Opens the sidebar
	* @return {this}
	*/
	open()
	{
		venus.input.unsetCookie(this.cookie_name);

		this.openObj();

		return this;
	}

	/**
	* @private
	*/
	openObj()
	{
		var self = this;

		self.aside_obj.removeClass('closed close-effect');
		this.main_obj.removeClass('open');
	}

	/**
	* Closes the sidebar
	* @return {this}
	*/
	close(effect)
	{
		venus.input.setCookie(this.cookie_name, 1, 365);

		this.setHeight();

		this.closeObj(effect);

		return this;
	}

	/**
	* @private
	*/
	closeObj(effect)
	{
		if(effect)
		{
			this.aside_obj.addClass('closed close-effect');

			this.main_obj.addClass('open open-effect');
		}
		else
		{
			this.aside_obj.addClass('closed');

			this.main_obj.addClass('open');
		}
	}

}