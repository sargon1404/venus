/**
* The Uri Class
* @author Venus-CMS
*/
class VenusUri {
	/**
	* Builds an url by appending params to url
	* @param {string} url The url
	* @param {mixed} params The params to append to url
	* @return {string} The url
	*/
	build (url, params) {
		if (!params) {
			return url;
		}

		if (!url.includes('?')) {
			url += '?';
		} else {
			url += '&';
		}

		url += this.buildParams(params);

		return url;
	}

	/**
	* Builds url params
	* @param {mixed} params The params to build
	* @return {string} The query string
	*/
	buildParams (params) {
		return jQuery.param(params);
	}

	/**
	* Adds www. to url, if it doesn't already have it
	* @param {string} url The url
	* @return {string} The url with www.
	*/
	addWww (url) {
		let scheme = this.getScheme(url);

		if (url.includes(scheme + '://www.')) {
			return url;
		}

		return scheme + '://www.' + url.slice(scheme.length + 3);
	}

	/**
	* Removes www. from url, if it has it
	* @param {string} url The url
	* @return {string} The url without www.
	*/
	stripWww (url) {
		let scheme = this.getScheme(url);

		return url.replace(scheme + '://www.', scheme + '://');
	}

	/**
	* Converts an url from www. to . as required. Usefull in iframes where calling a www. iframe from a non-www url might cause a security trigger
	* @param {string} url The url to redirect to
	* @return {string} The url
	*/
	convert (url) {
		let current_url = window.location.href;
		let scheme = this.getScheme(current_url);

		if (current_url.inclues(scheme + '://www.')) {
			return this.addWww(url);
		} else {
			return this.stripWww(url);
		}
	}

	/**
	* Returns the scheme [http|https] of url
	* @param {string} url The url
	* @return {string} The scheme
	*/
	getScheme (url) {
		if (url.includes('https://')) {
			return 'https';
		}

		return 'http';
	}
}
