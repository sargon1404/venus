<?php
/**
* The Cache trait
* @package Venus
*/

namespace Venus\Assets;

use Venus\App;

/**
* The Cache trait
* Contains functionality for allowing css/js files to be read/written to the cache folder
*/
trait CacheTrait
{

	/**
	* @var string $extension The extension of the files
	*/
	/*protected	$extension = '';*/

	/**
	* @var string $base_cache_url The url of the base/frontend cache url
	*/
	protected $base_cache_url = '';

	/**
	* @var string $cache_url The url of the cache dir
	*/
	protected $cache_url = '';

	/**
	* Returns the name under which a file will be cached
	* @param string $name The name of the file
	* @param array $params Extra params
	* @param string $device The device used
	* @return string
	*/
	public function getFile(string $name, array $params = [], string $device = '') : string
	{
		$parts = array_merge([$name, $device], $params);

		$parts = array_filter($parts);

		return implode($parts, '-') . '.' . $this->extension;
	}

	/**
	* Returns the name under which a library's code is cached
	* @param string $name The name of the library
	* @return string
	*/
	public function getLibraryFile(string $name) : string
	{
		return $this->getFile('library', [$name]);
	}

	/**
	* Returns the name under which a library's dependency css/js code is cached
	* @param string $name The name of the library
	* @return string
	*/
	public function getLibraryDependencyFile(string $name) : string
	{
		return $this->getFile('library', [$name, 'dependencies']);
	}

	/**
	* Returns the url of a library from the cache folder
	* @param string $name The name of the library
	* @return string The url
	*/
	public function getLibraryUrl(string $name) : string
	{
		return $this->app->cache_url . App::CACHE_DIRS['libraries'] . $this->getLibraryFile($name);
	}

	/**
	* Returns the url of of the file containing the css dependencies of a javascript library
	* @param string $name The name of the library
	* @return string
	*/
	public function getLibraryDependenciesUrl(string $name) : string
	{
		return $this->app->cache_url . App::CACHE_DIRS['libraries'] . $this->getLibraryDependencyFile($name);
	}

	/**
	* Returns the name of the file where a theme's css/js code will be cached
	* @param string $name The name of the theme
	* @param string $device The device
	* @return string
	*/
	public function getThemeFile(string $name, string $device) : string
	{
		return $this->getFile('theme', [$name], $device);
	}
}
