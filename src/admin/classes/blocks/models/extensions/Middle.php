<?php
/**
* The Middle model class
* @package Venus
*/

namespace Venus\Admin\Blocks\Models\Extensions;

use venus\admin\extensions\Installer;

/**
* The Middle model class
* Provides functionality shared by the Available and Listing models
*/
abstract class Middle
{
	/**
	* Returns the installer used to install this item
	* @return Installer The installer
	*/
	public function get_default_installer($item = null) : Installer
	{
		die("ooooo");
		return new Installer($item);
	}

	/**
	* Checks if the installer is of the correct type
	* @param object $installer The installer
	* @param string $class_name The required class
	*/
	public function check_installer($installer, $class_name)
	{
	}

	/**
	* Processes the item
	* @param object $item The item to process
	* @param object $installer The installer
	*/
	public function process_item($item, $installer)
	{
		return $item;
	}
}
