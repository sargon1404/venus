<?php
/**
* The Assets Generators File Class
* @package Venus
*/

namespace Venus\Assets\Generators;

/**
* The Assets Generators File Class
*/
abstract class File
{
	use \Venus\AppTrait;

	/**
	* @var string $extension The extension of the files
	*/
	protected string $extension = '';

	/**
	* Returns the name under which a file will be cached
	* @param string|array $parts The parts of the file name
	* @return string
	*/
	public function getFile(string|array $parts) : string
	{
		if (!is_array($parts)) {
			$parts = [$parts];
		}

		$parts = array_filter($parts);

		return implode('-', $parts) . '.' . $this->extension;
	}
}
