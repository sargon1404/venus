<?php
/**
* The Library Class
* @package Venus
*/

namespace Venus;

/**
* The Library Class
* Loader for css/javascript libraries
*/
class Library
{
	use AppTrait;

	/**
	* @var array $available_librarie Array listing the available libraries
	*/
	protected $available_libraries = [];

	/**
	* @var string $version The version to be applied to the css/js urls
	*/
	protected $version = '';

	/**
	* Builds the library object
	* @param App $app The app object
	*/
	public function __construct(App $app = null)
	{
		$this->app = $app;

		$this->available_libraries = $this->app->cache->getLibraries();
		$this->version = $this->app->cache->libraries_version;

		if ($this->app->development) {
			$this->version = time();
		}
	}

	/**
	* Loads a css library
	* @param $name The name of the library. Eg: bootstrap
	* @return $this
	*/
	public function loadCss(string $name)
	{
		if (!isset($this->available_libraries['css'][$name])) {
			throw new \Exception("Css library {$name } was not found. You might need to delete the cache");
		}

		$data = $this->available_libraries['css'][$name];

		$url = $this->app->css->getLibraryUrl($name);

		$this->app->plugins->run('libraryLoadCss1', $name, $url, $data);

		$this->app->css->load($url, $data['location'], $data['priority'], $this->version);

		if ($data['dependencies']) {
			$url = $this->app->javascript->getLibraryDependenciesUrl($name);

			$this->app->plugins->run('libraryLoadCss2', $name, $url, $data);

			$this->app->javascript->load($url, $data['dependencies']['location'], $data['dependencies']['priority'], $this->version, $data['dependencies']['async'], $data['dependencies']['defer']);
		}

		$this->app->plugins->run('libraryLoadCss3', $name, $data);

		return $this;
	}

	/**
	* Unloads a css library
	* @param $name The name of the library
	* @return $this
	*/
	public function unloadCss(string $name)
	{
		if (!isset($this->available_libraries['css'][$name])) {
			throw new \Exception("Css library {$name } was not found. You might need to delete the cache");
		}

		$data = $this->available_libraries['css'][$name];

		$url = $this->app->css->getLibraryUrl($name);
		$this->app->css->unload($url);

		if ($data['dependencies']) {
			$url = $this->app->javascript->getLibraryDependenciesUrl($name);
			$this->app->javascript->unload($url);
		}

		$this->app->plugins->run('libraryUnloadCss', $name);

		return $this;
	}

	/**
	* Loads a javascript library
	* @param string $name The name of the library. Eg: jquery
	* @return $this
	*/
	public function loadJavascript(string $name)
	{
		if (!isset($this->available_libraries['javascript'][$name])) {
			throw new \Exception("Javascript library {$name } was not found. You might need to delete the cache");
		}

		$data = $this->available_libraries['javascript'][$name];

		$url = $this->app->javascript->getLibraryUrl($name);

		$this->app->plugins->run('libraryLoadJavascript1', $name, $url, $data);

		$this->app->javascript->load($url, $data['location'], $data['priority'], $this->version, $data['async'], $data['defer']);

		if ($data['dependencies']) {
			$url = $this->app->css->getLibraryDependenciesUrl($name);

			$this->app->plugins->run('libraryLoadJavascript2', $name, $url, $data);

			$this->app->css->load($url, $data['dependencies']['location'], $data['dependencies']['priority'], $this->version);
		}

		$this->app->plugins->run('libraryLoadJavascript3', $name);

		return $this;
	}

	/**
	* Unloads a javascript library
	* @param $name The name of the library
	* @return $this
	*/
	public function unloadJavascript(string $name)
	{
		if (!isset($this->available_libraries['javascript'][$name])) {
			throw new \Exception("Javascript library {$name } was not found. You might need to delete the cache");
		}

		$data = $this->available_libraries['javascript'][$name];

		$url = $this->app->javascript->getLibraryUrl($name);
		$this->app->javascript->unload($url);

		if ($data['dependencies']) {
			$url = $this->app->css->getLibraryDependenciesUrl($name);
			$this->app->css->unload($url);
		}

		$this->app->plugins->run('libraryUnloadJavascript', $name);

		return $this;
	}
}
