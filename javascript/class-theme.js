/**
* The Theme Class
* @author Venus-CMS
* @property {string} name The name of the theme
* @property {string} dir_url The url to the theme's dir
* @property {string} images_url The url to the theme's images dir
* @property {object} params The theme's params
*/
class VenusTheme {
	/**
	* Returns the url of an image. If image_url contains the http(s):// part it is considered an url and returned directly.
	* If image_url doesn't contain http(s):// it is considered a theme image and the url of that image is returned
	* @param {string} image_url The image url
	* @param {string} [dir] Dir inside the images dir from where to return the image. Optional
	* @return {string} The image url
	*/
	getImage (image_url, dir) {
		let scheme = venus.uri.getScheme(image_url);

		if (!image_url.includes(scheme + '://')) {
			if (dir) {
				return this.images_url + dir + '/' + image_url;\
			} else {
				return this.images_url + image_url;
			}
		} else {
			return image_url;
		}
	}
}
