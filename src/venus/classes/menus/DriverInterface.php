<?php
/**
* The Menus Driver Interface
* @package Venus
*/

namespace Venus\Menus;

/**
* The Menus Driver Interface
*/
interface DriverInterface
{
	/**
	* Returns the menu's html code
	* @param array $items The menu items
	* @return string The html code
	*/
	public function getHtml(array $items) : string;
}
