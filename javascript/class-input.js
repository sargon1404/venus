/**
* The Input Class
* @author Venus-CMS
*/
class VenusInput
{

	/**
	* Reads the value of a cookie
	* @param {string} name The name of the cookie
	* @return {string} The cookie's value
	*/
	getCookie(name)
	{
		if(!document.cookie)
			return '';

		var start = document.cookie.indexOf(name + "=");
		if(start == -1)
			return '';

		 start = start + name.length + 1;
	    var end = document.cookie.indexOf(';', start);
	    if(end == -1)
	    	end = document.cookie.length;

	    var value = document.cookie.substring(start, end);

		return decodeURIComponent(value);
	}

	/**
	* Writes a cookie
	* @param {string} name The name of the cookie
	* @param {value} value The value of the cookie
	* @param {days} [days=0] days The number of days in which the cookie is valid
	* @param {path} [path=''] path The cookie's path
	* @return {this}
	*/
	setCookie(name, value, days, path)
	{
		var expires = '';
		var path = '';

		if(days)
		{
			var date = new Date();
			date.setTime(date.getTime() + (days * 24 * 3600 * 1000));

			expires = "; expires=" + date.toGMTString();
		}

		if(path)
			path = '; path=' + path;

		var cookie = name + "=" + encodeURIComponent(value) + expires + path;

		document.cookie = cookie;

		return this;
	}

	/**
	* Deletes a cookie
	* @param {string} name The name of the cookie to unset
	* @return {this}
	*/
	unsetCookie(name)
	{
		this.setCookie(name, '', -1);

		return this;
	}

}