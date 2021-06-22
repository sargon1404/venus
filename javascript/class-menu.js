/**
* The Menu Class
* @author Venus-CMS
*/
class VenusMenu {
	/**
	* Builds a menu as a mobile menu
	* @param {string} menu_id The id of the menu
	* @return {this}
	*/
	build (menu_id) {
		let self = this;

		jQuery('.toggle-menu').click(function(e) {
			var id = jQuery(this).data('target');

			self.toggleMenu(venus.get(id));

			e.stopPropagation();
		});

		venus.get(menu_id).find('li.has-dropdown > a').each(function () {
			jQuery(this).click(function (e) {
				var open = jQuery(this).parent().hasClass('open-dropdown');

				self.closeDropdowns(menu_id);

				if (!open) {
					jQuery(this).parent().addClass('open-dropdown');
				}

				e.stopPropagation();
			});
		});

		venus.get(menu_id).find('.nav-dropdown-close').each(function (){
			jQuery(this).click(function (e) {
				self.closeDropdowns(menu_id);

				e.stopPropagation();
			});
		});

		return this;
	}

	/**
	* Determines if an element has an open dropdown
	* @param {object} The element
	* @private
	*/
	hasOpenDropdowns(element) {
		return jQuery(element).find('.nav-dropdown.nav-dropdown-open').length;
	}

	/**
	* Closes all the open dropdowns of a menu
	* @param {string} menu_id The id of the menu
	* @return {this}
	*/
	closeDropdowns(menu_id) {
		venus.get(menu_id).find('.open-dropdown').each(function () {
			jQuery(this).removeClass('open-dropdown');
		});
	}

	/**
	* @private
	*/
	toggleMenu (obj) {
		obj.toggle();
	}
}
