/**
* The Venus Class
* @author Venus-CMS
* @property {string} device The device the user is using. Eg: pc/tablet/smartphone
* @property {string} browser The user's browser
* @property {object} config The config options
* @property {object} lang The language object. Strings can be accessed with venus.lang.strings
* @property {bool} debug If true, will run the code in debug mode
* @property {bool} development If true, will run the code in development mode
* @property {object} content_obj The content object
* @property {object} content_inner_obj The content inner object
* @property {object} main_obj The main object
* @property {string} site_url The site's url
* @property {string} site_url_static The url from where static content is served
* @property {string} images_url The url to the images dir
* @property {string} uploads_url The url to the uploads dir
* @property {string} media_url The url to the media dir
* @property {string} assets_url The url to the assets dir
* @property {string} utils_url The url to the utils dir
*/
class Venus {
	constructor () {
		this.config = {};
		this.lang = {strings: {}};
		this.device = 'pc';
		this.browser = this.getBrowser();
		this.debug = false;
		this.development = false;
		this.content_obj = null;
		this.content_inner_obj = null;
		this.main_obj = null;

		this.id_index = 0;
		this.keep_alive_interval_handle = 0;

		// read the ready_funcs from the inline venus object, if defined
		this.ready_funcs = [];
		if (venus !== undefined) {
			this.ready_funcs = venus.ready_funcs;
		}
	}

	/**
	* Function to be executed when the document is ready
	* @param {function} func The function to execute
	* @return {this}
	*/
	ready (func) {
		jQuery(document).ready(func);

		return this;
	}

	/**
	* Logs data to the console
	* @param {mixed} data The data to log
	* @param {string} text Text to display
	* @return {this}
	*/
	log (data, text) {
		if (text) {
			console.log(text);
		}

		console.log(data);
		console.log('----------------------------------------');

		return this;
	}

	/**
	* Prepares the venus object
	*/
	prepare () {
		let self = this;

		this.ready(function () {
			// set the content/contentInner/main objects for easy access
			self.content_obj = self.get('content');
			self.content_inner_obj = self.get('content-inner');
			self.main_obj = self.getTag('main');
			if (!self.main_obj.length) {
				self.main_obj = self.get('main');
			}
		});

		jQuery(document).click(function () {
			// hide the populate popup on click
			self.populate.close();
			self.list.close();
		});
	}

	/**
	* Inits the tooltips/modals
	* @param {string|object} [element] Optional element over which the tooltips/modals will be displayed. If not specified, document is used
	*/
	initHtml (element) {
		if (this.tooltips.are_enabled) {
			this.tooltips.init(element);
		}

		if (this.modals.are_enabled) {
			this.modals.init(element);
		}
	}

	/**
	* Returns the name of the browser the user is using.
	* @return The browser's name [firefox | chrome| ie | opera | safari | other]
	*/
	getBrowser () {
		if (navigator.userAgent.toLowerCase().search('chrome') != -1) {
			return 'chrome';
		} else if (navigator.userAgent.toLowerCase().search('firefox') != -1) {
			return 'firefox';
		} else if (navigator.userAgent.toLowerCase().search('msie') != -1) {
			return 'ie';
		} else if (navigator.userAgent.toLowerCase().search('trident') != -1) {
			return 'ie';
		} else if (navigator.userAgent.toLowerCase().search('opera') != -1) {
			return 'opera';
		} else if (navigator.userAgent.toLowerCase().search('safari') != -1) {
			return 'safari';
		} else {
			return 'other';
		}
	}

	/**
	* Returns an unique id
	* @param {string} [prefix] A prefix to use, if any
	* @return {string} The unique id
	*/
	generateId (prefix) {
		this.id_index += 1;

		if (prefix) {
			prefix = prefix + '-';
		} else {
			prefix = '';
		}

		return prefix + this.randStr() + '-' + this.id_index;
	}

	/**
	* Returns a random string
	* @return {string} The random string
	*/
	randStr () {
		let str = Math.random().toString(36);
		str = str.substring(2, str.length);

		return str;
	}

	/**
	* Returns a random number
	* @param {int} The min interval
	* @param {int} The max interval
	* @return {int} The random number
	*/
	randInt (min, max) {
		return Math.floor(Math.random() * (max - min + 1)) + min;
	}

	/**
	* Encodes an object using json
	* @param {mixed} data The data to encode
	* @return The encoded content
	*/
	encode (data) {
		return JSON.stringify(data);
	}

	/**
	* Decodes json data
	* @param {string} data The data to decode
	* @return mixed The decoded json data
	*/
	decode (data) {
		if (typeof data == 'string') {
			return JSON.parse(data);
		} else {
			return data;
		}
	}

	/**
	* Returns an object
	* @param {string|object} id The object's id
	* @return object The jquery object
	*/
	get (id) {
		if (typeof id == 'string') {
			return jQuery('#' + id);
		}

		return jQuery(id);
	}

	/**
	* Returns the DOM object. get() returns a jquery object, getDom() returns getElementById()
	* @param {string|object} id The object's id
	* @return object The DOM object
	*/
	getDom (id) {
		if (typeof id == 'string') {
			return document.getElementById(id);
		}

		return id;
	}

	/**
	* Returns the object(s), based on tag
	* @param {string} tag The tag to search for
	* @return object The object(s)
	*/
	getTag (tag) {
		return jQuery(tag);
	}

	/**
	* Returns object(s) by selector
	* @param {string} selector The selector
	* @param {string|object} element The element to restrict the search to
	* @return object The jquery object
	*/
	getSelector (selector, element) {
		if (element) {
			return this.get(element).find(selector);
		} else {
			return jQuery(selector);
		}
	}

	/**
	* Detects if an element is visible
	* @param {string|object} id The id
	* @return bool Returns true if the object is visible
	*/
	isVisible (id) {
		return this.get(id).is(':visible');
	}

	/**
	* Detects if an element with a certain id exists in the dom tree
	* @param {string} id The id
	* @return bool Returns true if the object exists
	*/
	exists (id) {
		let obj = this.get(id);

		if (obj.length) {
			return true;
		} else {
			return false;
		}
	}

	/**
	* Trims a string
	* @param {string} text The text to trim
	* @return {string} The trimmed text
	*/
	trim (text) {
		text = text.replace(/^\s+/, '');
		text = text.replace(/\s+$/, '');

		return text;
	}

	/**
	* Sanitizes text. Replaces ' with \'
	* @param {string} text The text to sanitize
	* @return {string} The sanitized text
	*/
	sanitize (text) {
		return text.replace('\'', '\\\'');
	}

	/**
	* Convert special characters to HTML entities
	* @param {string} text The text to convert
	* @return {string} The converted text
	*/
	htmlspecialchars (text) {
		text = text.replace(/&/g, '&amp;');
		text = text.replace(/"/g, '&quot;');
		text = text.replace(/</g, '&lt;');
		text = text.replace(/>/g, '&gt;');
		text = text.replace(/'/g, '&#39;');
		text = text.replace(/\//g, '&#x2F;');

		return text;
	}

	/**
	* Convert special HTML entities back to characters
	* @param {string} text The text to convert
	* @return {string} The converted text
	*/
	htmlspecialcharsDecode (text) {
		text = text.replace(/&amp;/g, '&');
		text = text.replace(/&quot;/g, '"');
		text = text.replace(/&lt;/g, '<');
		text = text.replace(/&gt;/g, '>');
		text = text.replace(/&#39;/g, '\'');
		text = text.replace(/&#x2F;/g, '/');

		return text;
	}

	/**
	* Appends part to text along with a dash, if part is not empty
	* @param {string} text The text
	* @param {string} part The part to append
	* @return {string}
	*/
	appendText (text, part) {
		if (!part) {
			return text;
		}

		return text + '-' + part;
	}

	/**
	* Redirects to url
	* @param {string} url The url to redirect to
	*/
	redirect (url) {
		window.location = url;
	}

	/**
	* Jumps to an element
	* @param {string} id The id of the element to jump to
	* @return {this}
	*/
	jump (id) {
		window.location.href = '#' + id;
		window.location.hash = id;

		return this;
	}

	/**
	* Returns the relative position of an *hidden* absolute positioned object relative to an element. If obj is not given, will only return the position of element
	* @param {string|object} element The relative element
	* @param {string|object} obj The object to get the position for
	* @return {object} The object's position {x, y}
	*/
	getPosition (element, obj) {
		element = this.get(element);

		let pos = element.offset();
		let x = pos.left;
		let y = pos.top + element.outerHeight();

		if (!obj) {
			return {x: x, y: y};
		}

		obj = this.get(obj);

		// move obj to a remote position, show it, get the outerWidth() then hide it
		obj.css({left: -9999, top: -9999});
		obj.show();

		let width = obj.outerWidth();
		let height = obj.outerHeight();

		obj.hide();

		if (x + width > jQuery(window).width()) {
			x = pos.left - width + element.outerWidth();
		}
		if (y + height > jQuery(window).height()) {
			y = pos.top - height;
		}

		return {x: x, y: y};
	}

	/**
	* Calls at each x minutes the keepAlive script using ajax
	* @param {int} minutes The number of minutes
	* @return {int} The id returned by setInterval
	*/
	keepAlive (minutes) {
		if (this.keep_alive_interval_handle) {
			return this.keep_alive_interval_handle;
		}

		this.keep_alive_interval_handle = setInterval(function () {
			venus.ajax.getUrl(venus.utils_url + 'keep_alive.php');
		}, minutes * 60000);

		return this.keep_alive_interval_handle;
	}
}
