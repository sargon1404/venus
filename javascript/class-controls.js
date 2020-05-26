/**
* The Controls Class
* @author Venus-CMS
*/
class VenusControls {
	/**
	* Filters the list
	*/
	filter () {
		let self = this;

		self.filterAjaxStart();

		venus.ajax.postForm('controls-filters-form', function (response) {
			venus.ajax.handleMain(response, function () {
				self.filterAjaxEnd();
			}, function () {
				self.filterAjaxEnd();
			});
		});
	}

	/**
	* Resets the filters
	*/
	filterReset () {
		let self = this;

		self.filterAjaxStart();

		venus.ajax.postForm('controls-filters-form', function (response) {
			venus.ajax.handleMain(response, function () {
				self.resetFilterFields();
				self.filterAjaxEnd();
			}, function () {
				self.filterAjaxEnd();
			});
		}, null, {reset: '1'});
	}

	/**
	* Resets the filter fields
	*/
	resetFilterFields () {
		let form = venus.get('controls-filters-form');

		form.find('input[type="text"]').each(function () {
			venus.get(this).val('');
		});

		form.find('select').each(function () {
			venus.get(this).val(venus.get(this).find('option:first').val());
		});
	}

	/**
	* @private
	*/
	filterAjaxStart () {
		venus.loading.showIcon('controls-filter-loading');
		venus.get('controls-filters-action').addClass('disabled');
	}

	/**
	* @private
	*/
	filterAjaxEnd () {
		venus.loading.hideIcon('controls-filter-loading');
		venus.get('controls-filters-action').removeClass('disabled');
	}

	/**
	* Orders the list
	*/
	order () {
		let self = this;

		self.orderAjaxStart();

		venus.ajax.postForm('controls-order-form', function (response) {
			venus.ajax.handleMain(response, function () {
				self.orderAjaxEnd();
			}, function () {
				self.orderAjaxEnd();
			});
		});
	}

	/**
	* Resets the order fields
	*/
	order_reset () {
		this.resetOrderFields();
		this.order();
	}

	/**
	* Resets the order fields
	*/
	resetOrderFields () {
		let form = venus.get('controls-order-form');

		form.find('select').each(function () {
			venus.get(this).val(venus.get(this).find('option:first').val());
		});
	}

	/**
	* @private
	*/
	orderAjaxStart () {
		venus.loading.showIcon('controls-order-loading');
		venus.get('controls-order-action').addClass('disabled');
	}

	/**
	* @private
	*/
	orderAjaxEnd () {
		venus.loading.hideIcon('controls-order-loading');
		venus.get('controls-order-action').removeClass('disabled');
	}

	/**
	* Sets the items per page
	*/
	itemsPerPage () {
		let self = this;

		self.itemsPerPageAjaxStart();

		venus.ajax.postForm('controls-items-per-page-form', function (response) {
			venus.ajax.handleMain(response, function () {
				self.itemsPerPageAjaxEnd();
			}, function () {
				self.itemsPerPageAjaxEnd();
			});
		});
	}

	/**
	* @private
	*/
	itemsPerPageAjaxStart () {
		venus.loading.showIcon('controls-items-per-page-loading');
		venus.get('controls-items-per-page-action').addClass('disabled');
	}

	/**
	* @private
	*/
	itemsPerPageAjaxEnd () {
		venus.loading.hideIcon('controls-items-per-page-loading');
		venus.get('controls-items-per-page-action').removeClass('disabled');
	}
}
