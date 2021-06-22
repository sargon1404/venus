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
	* @param string $name The menu's name
	* @param array $items The menu's items
	* @return string The html code
	*/
	public function getHtml(string $name, array $items) : string;
}
