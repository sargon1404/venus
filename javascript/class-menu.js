/**
* The Menu Class
* @author Venus-CMS
*/
class VenusMenu
{

	/**
	* Toggles a mobile menu
	* @param {string} menu_id The id of the menu
	* @return {this}
	*/
	toggle(menu_id)
	{
		var obj = venus.get(menu_id + '-container');

		this.toggleObj(obj);

		return this;
	}

	/**
	* @private
	*/
	toggleObj(obj)
	{
		obj.toggle();
	}

	/**
	* Builds a menu as a mobile menu
	* @param {string} menu_id The id of the menu
	* @return {this}
	*/
	build(menu_id)
	{
		var self = this;
		venus.get(menu_id).find('li').each(function(){

			jQuery(this).click(function(e){

				var href = jQuery(this).children('a').first().attr('href');

				if(href == '' || href == '#' || href == 'javascript:void(0)')
				{
					//open the menu only if we're having a real url
					jQuery(this).children('ul').each(function(){
						self.toggleChildObj(jQuery(this));
					});
				}

				e.stopPropagation();
			});

		});

		return this;
	}

	/**
	* @private
	*/
	toggleChildObj(obj)
	{
		obj.toggle();
	}

}