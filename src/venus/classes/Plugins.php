<?php
/**
* The Plugins Class
* @package Venus
*/

namespace Venus;

/**
* The Plugins Class
* Loads the plugins and executes the hooks
*/
class Plugins extends Items
{
	/**
	* @internal
	*/
	protected static $id_name = 'pid';

	/**
	* @internal
	*/
	protected static $table = 'venus_plugins';

	/**
	* @internal
	*/
	protected static $class = '\Venus\Plugin';
}
