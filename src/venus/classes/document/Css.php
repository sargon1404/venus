<?php
/**
* The Css Urls Class
* @package Venus
*/

namespace Venus\Document;

use Venus\App;

/**
* The Document's Css Urls Class
* Class containing the css urls/stylesheets used by a document
*/
class Css extends \Mars\Document\Css
{
	use \Venus\Assets\CacheTrait;


	/**
	* Builds the css object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		$this->setCacheUrls();
	}
	
	/**
	* Returns the name of the file where a theme's css vars will be cached
	* @param string $name The name of the theme
	* @param string $device The device
	* @return string
	*/
	public function getThemeVarsFile(string $name, string $device) : string
	{
		return $this->getFile('theme', $device, [$name]) . '.vars';
	}
}
