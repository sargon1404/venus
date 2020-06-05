<?php
/**
* The Mvc Trait
* @package Venus
*/

namespace Venus;

/**
* The Mvc Trait.
* Shared functionality between controllers/models/views
* @package Venus
*/
trait MvcTrait
{
	/**
	* @var string $prefix Prefix to be used when calling plugins
	*/
	public string $prefix = '';

	/**
	* Returns a prefix out of the class's name
	* @param string $suffix The suffix to append, if any
	* @return string The prefix
	*/
	protected function getPrefix(string $suffix = '') : string
	{
		$prefix = $this->prefix;

		if (!$prefix) {
			$prefix = str_replace("\\", '_', strtolower($this->getClassName()));
		}

		if ($suffix) {
			$prefix.= '_' . $suffix;
		}

		return $prefix . '_';
	}
}
