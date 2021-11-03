<?php
/**
* The Css Libraries Generator Class
* @package Venus
*/

namespace Venus\Assets\Generators\Css\Sources;

use Venus\App;

/**
* The Css Libraries Generator Class
* Class generating the cache code for each library
*/
class Libraries extends Base
{
	/**
	* @var string $libraries_dir The folder where the libraries of this type are located
	*/
	protected string $libraries_dir = 'css';

	/**
	* {@inheritdoc}
	* @see \Venus\Assets\Generators\Css\Sources\Base::__construct()
	*/
	public function __construct(App $app)
	{
		parent::__construct($app);
	}

	/**
	* Returns the list of urls which have been generated
	* @return array
	*/
	public function getUrls() : array
	{
		$urls = [];
		die("get libraries urls");
		return $urls;
	}

	/**
	* Generates the cache for each source
	*/
	public function cache()
	{
		//var_dump($this->app->dir->copy('/var/www/test', '/var/www/test-copy'));
		var_dump($this->app->dir->delete('/var/www/test-copy'));
		die;

		//$libraries = $this->app->dir->getDirs($this->app->libraries_path . 'css');
		$libraries = $this->app->dir->getFiles($this->app->libraries_path . 'css');
		print_r($libraries);
		die;
		foreach ($libraries as $library) {
		}
	}

	/**
	* Caches the files of a library
	* @param string $name The name of the library
	* @param string $files The library's files
	* @param return $dependencies_files The library's dependency files
	*/
	public function cacheLibrary(string $name, array $files, array $dependencies_files = [])
	{
		$code = $this->mergeLibraryFiles($name, $files);

		$cache_file = $this->getLibraryFile($name, $this->extension);

		$this->storeLibrary($cache_file, $code);

		if ($dependencies_files) {
			$dependencies_code = $this->mergeLibraryFiles($name, $dependencies_files);

			$obj = $this->getDependenciesHandler();

			$cache_file = $obj->getLibraryDependencyFile($name);

			$obj->storeLibrary($cache_file, $dependencies_code);
		}
	}
}
