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
	* @param string $name The name of the file
	* @param array $params Extra params
	* @return string
	*/
	public function getFile(string $name, array $params = []) : string
	{
		$parts = array_merge([$name], $params);

		$parts = array_filter($parts);

		return implode('-', $parts) . '.' . $this->extension;
	}
}