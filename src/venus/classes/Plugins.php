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
	protected static string $id_name = 'pid';

	/**
	* @internal
	*/
	protected static string $table = 'venus_plugins';

	/**
	* @internal
	*/
	protected static string $class = '\Venus\Plugin';
}
