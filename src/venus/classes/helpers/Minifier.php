<?php
/**
* The Minifier Class
* @package Venus
*/

namespace Venus\Helpers;

/**
* The Asset Minifier Class
* Minifies assets content
*/
class Minifier extends \Mars\Helpers\Minifier
{
	/**
	* Minifies css code
	* @param string $code The css code to minify
	* @return string The minified css code
	*/
	public function minifyCss(string $code) : string
	{
		$minifier = new \MatthiasMullie\Minify\CSS;
		$minifier->add($code);

		return $minifier->minify();
	}

	/**
	* Minifies javascript code
	* @param string $code The js code to minify
	* @return string The minified js code
	*/
	public function minifyJavascript(string $code) : string
	{
		$minifier = new \MatthiasMullie\Minify\JS;
		$minifier->add($code);

		return $minifier->minify();
	}
}
