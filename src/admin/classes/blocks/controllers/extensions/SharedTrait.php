<?php
/**
* The Shared trait
* @package Venus
*/

namespace Venus\Admin\Blocks\Controllers\Extensions;

/**
* The Shared trait
* Provides functionality shared by the Available' and Listing' controllers
*/
trait SharedTrait
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
