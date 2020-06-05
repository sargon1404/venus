<?php
/**
* The Middle controller class
* @package Venus
*/

namespace Venus\Admin\Blocks\Controllers\Extensions;

/**
* The Middle controller class
* Provides functionality shared by the Available' and Listing' controllers
*/
abstract class Middle
{
	use \Venus\AppTrait;

	/**
	* Outputs errors
	* @param array $errors The errors, if any
	*/
	public function outputErrors(array $errors)
	{
		$this->app->errors->add($errors);
	}
}
