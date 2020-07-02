<?php
/**
* The App Functions Trait
* @package Venus
*/

namespace Venus;

/**
* The App Functions
* Contains the App static functions
*/
trait AppFunctionsTrait
{
	/**
	* Returns the current time(), adjusted to the user's timezone
	* @see \Venus\Time\current()
	* @return int
	*/
	public static function time() : int
	{
		return static::$instance->time->current();
	}
}
