<?php
/**
* The Base model for admin blocks
* @package Venus
*/

namespace Venus\Admin\Blocks\Models;

/**
* The Base model for admin blocks
*/
abstract class Base extends \Venus\Admin\Model
{
	/**
	* Validates the item's field during a set operation
	* @param object $item The item to validate
	*/
	//object
	protected function update_set_validate_item($item)
	{
		return $item->validate(['title']);
	}
}
