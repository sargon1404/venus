<?php
/**
* The Vertical With Blocks Menu Driver
* @package Venus
*/

namespace Venus\Menus;

use Venus\App;

/**
* The Vertical With Blocks Menu Driver
*/
class Vertical extends Menu implements DriverInterface
{
	/**
	* @var $class The menu's class attribute
	*/
	protected string $class = 'nav-vertical';
}
