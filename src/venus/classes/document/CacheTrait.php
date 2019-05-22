<?php
/**
* The Cache trait
* @package Mars
*/

namespace Venus\Document;

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
	* Builds the javascript/css cache object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		$this->base_cache_url = $this->app->cache_url;
		$this->cache_url = $this->base_cache_url;
	}

	/**
	* Returns the url of a library from the cache folder
	* @param string $name The name of the library
	* @return string The url
	*/
	public function getLibraryUrl()
	{
		return $this->base_cache_url . 'javascript/' . $this->getLibraryFile($name, 'js');
		var_dump($this);
		die;
	}

	/**
	* Returns the name under which a file will be cached
	* @param string $name The name of the file
	* @param string $device The device used
	* @param array $params Extra params
	* @return string
	*/
	public function getFile(string $name, string $device, array $params = []) : string
	{
		$parts = array_merge([$name, $device], $params);

		$parts = array_filter($parts);

		return implode($parts, '-') . $this->extension;
	}

	/**
	* Returns the name of the file where a theme's css code will be cached
	* @param string $name The name of the theme
	* @param string $device The device
	* @return string
	*/
	public function getThemeFile(string $name, string $device) : string
	{
		return $this->getFile('theme', $device, [$name]);
	}
}
