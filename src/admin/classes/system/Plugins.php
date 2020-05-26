<?php
/**
* The System Plugins Class
* @package Venus
*/

namespace Venus\Admin\System;

/**
* The System Plugins Class
* Container for the system's plugins
*/
class Plugins extends \Venus\System\Plugins
{

	/**
	* @var string $scope The scope where the plugins will be loaded
	*/
	protected string $scope = 'admin';
}
