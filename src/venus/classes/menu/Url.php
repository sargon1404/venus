<?php
/**
* The Menu Url Base Class
* @package Venus
*/

namespace Venus\Menu;

/**
* The Menu Url Base Class
*/
abstract class Url
{
	use \Venus\AppTrait;

	/**
	* Returns the url of a menu item
	* @return string The url
	*/
	abstract public function getUrl(object $item) : string;
}
