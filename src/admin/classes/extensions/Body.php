<?php
/**
* The Extension Body "Class"
* @package Venus
*/

namespace Venus\Admin\Extensions;

use Venus\Admin\App;

/**
* The Extension Body "Class"
* Contains the functionality for classes Extension/Basic
*/
trait Body
{
	/**
	* Returns the root dir where extensions of this type are located
	*/
	public function getRootDir() : string
	{
		return $this->app->admin_extensions_dir;
	}

	/**
	* Returns the root url where extensions of this type are located
	*/
	public function getRootUrl() : string
	{
		return $this->app->admin_extensions_url;
	}

	/**
	* Returns the static root url where extensions of this type are located
	*/
	public function getRootUrlStatic() : string
	{
		return $this->app->uri->stripScheme($this->app->admin_extensions_url);
	}
}
