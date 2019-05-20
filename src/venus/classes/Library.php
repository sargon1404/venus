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
	* @param array Array listing the loaded libraries
	*/
	//protected	$libraries = ['css' => [], 'javascript' => []];

	/**
	* @param array Array listing the available libraries
	*/
	protected $available_libraries = [];

	/**
	* Builds the library object
	* @param App $app The app object
	*/
	public function __construct(App $app = null)
	{
		$this->app = $app;

		$this->available_libraries = $this->app->cache->getLibraries();
	}

	/**
	* Returns the list of loaded javascript libraries
	* @return array
	*/
	/*public function getCss() : array
	{
		return $this->get('css');
	}*/

	/**
	* Returns the css dependencies of the javascript libraries
	* @return array
	*/
	/*public function getCssDependencies() : array
	{
		return $this->getDependencies('javascript');
	}*/

	/**
	* Returns the libraries which have dependencies of a certain type
	* @param string $type The type of libraries to return the dependencies for [css|javascript]
	* @return array
	*/
	/*public function getDependencies(string $type) : array
	{
		$dependencies = [];
		$libraries = $this->get($type);

		foreach($libraries as $library => $data)
		{
			if(isset($this->available_libraries[$type][$library]))
			{
				if($this->available_libraries[$type][$library]['dependencies'])
					$dependencies[$library] = $data;
			}
		}

		return $dependencies;
	}*/

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

		$url = $this->app->cache->getCssLibraryUrl($name);

		$this->app->plugins->run('libraryLoadCss1', $name, $url, $data);

		$this->app->css->load($url, $data['location'], $data['priority']);

		if ($data['dependencies']) {
			$url = $this->app->cache->getCssLibraryDependenciesUrl($name);

			$this->app->plugins->run('libraryLoadCss2', $name, $url, $data);

			$this->app->javascript->load($url, $data['dependencies']['location'], $data['dependencies']['priority'], $data['dependencies']['async'], $data['dependencies']['defer']);
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

		$url = $this->app->cache->getCssLibraryUrl($name);
		$this->app->css->unload($url);

		if ($data['dependencies']) {
			$url = $this->app->cache->getCssLibraryDependenciesUrl($name);
			$this->app->javascript->unload($url);
		}

		$this->app->plugins->run('libraryUnloadCss', $name);

		return $this;
	}

	/**
	* Returns the list of loaded javascript libraries
	* @return array
	*/
	/*public function getJavascript() : array
	{
		return $this->get('javascript');
	}*/

	/**
	* Returns the javascript dependencies of the css libraries
	* @return array
	*/
	/*public function getJavascriptDependencies() : array
	{
		return $this->getDependencies('css');
	}*/

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

		//$url = $this->app->cache->getJavascriptLibraryUrl($name);

		$this->app->plugins->run('libraryLoadJavascript1', $name, $url, $data);

		$this->app->javascript->load($url, $data['location'], $data['priority'], $data['async'], $data['defer']);

		if ($data['dependencies']) {
			$url = $this->app->cache->getJavascriptLibraryDependenciesUrl($name);

			$this->app->plugins->run('libraryLoadJavascript2', $name, $url, $data);

			$this->app->css->load($url, $data['dependencies']['location'], $data['dependencies']['priority']);
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

		$url = $this->app->cache->getJavascriptLibraryUrl($name);
		$this->app->javascript->unload($url);

		if ($data['dependencies']) {
			$url = $this->app->cache->getJavascriptLibraryDependenciesUrl($name);
			$this->app->css->unload($url);
		}

		$this->app->plugins->run('libraryUnloadJavascript', $name);

		return $this;
	}
}
