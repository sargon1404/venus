/**
* The Tabs Class
* @author Venus-CMS
*/
class VenusTabs {
	constructor () {
		this.current_tab_id = 1;
	}

	/**
	* Sets the id of the current tab
	* @param {string} tab_id The id of the current tab
	*/
	set (tab_id) {
		this.current_tab_id = tab_id;
	}

	/**
	* Sets the current tab
	*/
	auto () {
		let self = this;
		jQuery(document).ready(function () {
			venus.get('tabs').children('a').each(function () {
				let id = venus.get(this).attr('id');
				let tab_id = id.replace('tab-', '');

				if (tab_id == self.current_tab_id) {
					venus.get('tab-' + tab_id).addClass('current');
					venus.get('tab-content-' + tab_id).show();
				} else {
					venus.get('tab-content-' + tab_id).hide();
				}
			});
		});
	}

	/**
	* Switches a tab
	* @param {string} tab_id The id of new tab to show
	*/
	switch (tab_id) {
		if (tab_id == this.current_tab_id) {
			return;
		}

		venus.get('tab-' + this.current_tab_id).removeClass('current');
		venus.get('tab-' + tab_id).addClass('current');

		venus.get('tab-content-' + this.current_tab_id).hide();
		venus.get('tab-content-' + tab_id).show();

		venus.get('tab-id').val(tab_id);

		this.current_tab_id = tab_id;
	}
}
