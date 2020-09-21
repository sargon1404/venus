/**
* The Menu Class
* @author Venus-CMS
*/
class VenusMenu {
	/**
	* Builds a menu as a mobile menu
	* @param {string} menu_id The id of the menu
	* @param {bool} for_mobile If true, will build the menu for mobile devices
	* @return {this}
	*/
	build (menu_id, for_mobile) {
		let self = this;

		jQuery('.toggle-menu').click(function(e) {
			var id = jQuery(this).data('target');

			self.toggleMenu(venus.get(id));

			e.stopPropagation();
		});

		if (!for_mobile) {
			return;
		}

		venus.get(menu_id).find('li').each(function () {
			jQuery(this).click(function (e) {
				let href = jQuery(this).children('a').first().attr('href');

				if (href == '' || href == '#' || href == 'javascript:void(0)') {
					// open the menu only if we're having a real url
					jQuery(this).children('ul').each(function () {
						self.toggleSubmenu(jQuery(this));
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
	toggleMenu (obj) {
		obj.toggle();
	}

	/**
	* @private
	*/
	toggleSubmenu (obj) {
		obj.toggle();
	}
}
