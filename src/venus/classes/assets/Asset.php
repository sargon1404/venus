<?php
/**
* The Asset Class
* @package Venus
*/

namespace Venus\Assets;

use Venus\App;

/**
* The Asset Class
* Abstract class containing functionality shared by all assets
*/
abstract class Asset
{
	use \Venus\AppTrait;
	//use CacheTrait;

	/**
	* @var string $dir The dir from where the assets will be loaded
	*/
	//protected string $dir = '';

	/**
	* @var string $extension The extension of the files for this type of asset
	*/
	//protected string $extension = '';

	/**
	* @var string $base_cache_path The folder where the assets will be cached, for the frontend
	*/
	//protected string $base_cache_path = '';

	/**
	* @var string $cache_path The folder where this assets will be cached
	*/
	//protected string $cache_path = '';

	/**
	* @var string $libraries_dir The folder where the libraries of this type are located
	*/
	protected string $libraries_dir = '';

	/**
	* @var bool $minify True, if the output can be minified
	*/
	//protected bool $minify = true;

	/**
	* Returns the asset responsible for handling the dependencies for libraries of this type
	* @return Asset The asset handling the dependencies
	*/
	abstract public function getDependenciesHandler() : Asset;

	/**
	* Returns the sources handler
	* @return
	*/
	abstract public function getGeneratorsHandler();

	/**
	* Combines & minifies & caches the css/javascript code
	*/
	public function buildCache()
	{
		$generators = $this->getGeneratorsHandler();
		$generators->cache();
	}























	/**
	* Stores content in the cache folder
	* @param string $file The name of the file
	* @param string $content The content to store
	* @param bool $minify If true, will minify the content. If null, $this->minify is used
	* @param bool $parse If true, will parse the content
	* @return bool
	*/
	public function store(string $file, string $content, ?bool $minify = null, bool $parse = true) : bool
	{
		return $this->storeFile($this->cache_path, $file, $content, false, $minify, $parse);
	}

	/**
	* Stores content in the cache folder
	* @param string $file The name of the file
	* @param string $content The content to store
	* @param bool $minify If true, will minify the content. If null, $this->minify is used
	* @param bool $parse If true, will parse the content
	* @return bool
	*/
	public function storeLibrary(string $file, string $content, ?bool $minify = null)
	{
		return $this->storeFile($this->app->cache_path . App::CACHE_DIRS['libraries'], $file, $content, false, $minify, false);
	}

	/**
	* Stores multiple files
	* @param string $file The name of the file where the content of the files will be stored
	* @param array $files The files to store
	* @param bool $minify If true, will minify the content. If null, $this->minify is used
	* @param bool $parse If true, will parse the content
	*/
	public function storeFiles(string $file, array $files, ?bool $minify = null, bool $parse = true)
	{
		$filename = $this->cache_path . $file;
		if (is_file($filename)) {
			unlink($filename);
		}

		foreach ($files as $file_data) {
			$content = file_get_contents($file_data['file']) . $this->merge_separator;

			$file_minify = $minify;
			$file_parse = $parse;
			$file_parse_params = [];

			//don't minify & parse the files from cache; these files should already be minified & parsed
			if ($file_data['cached']) {
				$file_minify = false;
				$file_parse = false;
			} else {
				$file_parse_params = ['url' => $file_data['url']];
			}

			$this->storeFile($this->cache_path, $file, $content, true, $file_minify, $file_parse, $file_parse_params);
		}
	}

	/**
	* Stores a file
	* @param string $dir The folder where the file will be created
	* @param string $file The name of the file
	* @param string $content The content to store
	* @param bool $append If true, will append the content
	* @param bool $minify If true, will minify the content. If null, $this->minify is used
	* @param bool $parse If true, will parse the content
	* @param array $parse_params Parse params, if any
	* @return bool
	*/
	protected function storeFile(string $dir, string $file, string $content, bool $append = false, ?bool $minify = null, bool $parse = true, array $parse_params = []) : bool
	{
		if ($parse) {
			$content = $this->parse($content, $parse_params);
		}

		if ($minify === null) {
			$minify = $this->minify;
		}
		if ($minify) {
			$content = $this->minify($content);
		}

		$flags = 0;
		if ($append) {
			$flags = FILE_APPEND;
		}

		$filename = $dir . $file;

		$content = $this->app->plugins->filter('assets_merge_trait_store_file', $content, $filename, $append, $minify, $parse, $flags);

		return file_put_contents($filename, $content, $flags);
	}

	/**
	* Minifes the content
	* @param string $content The content to minify
	* @return string The minified content
	*/
	public function minify(string $content) : string
	{
		return $content;
	}

	/**
	* Parses the content
	* @param string $content The content to parse
	* @param array $params Parse params, if any
	* @return string The parsed content
	*/
	public function parse(string $content, array $params = []) : string
	{
		return $content;
	}

	/**
	* Parses inline content
	* @param string $content The content to parse
	* @return string The parsed content
	* @return string $device The device for which the content is parsed
	*/
	public function parseInline(string $content, string $device) : string
	{
		return $content;
	}


	/**
	* Returns the inline code of a theme
	* @param string $dir The dir from where to read the inline code
	* @param bool $minify If true, the code will be minified
	* @return array The inline code for each device
	*/
	protected function getInline(string $dir, bool $minify = true) : array
	{
		$inline_code = [];

		$inline_dir = $dir . App::EXTENSIONS_DIRS['inline'];
		$inline_code['desktop'] = $this->readFromDir($inline_dir);

		//get the inline content for each device
		$devices = $this->app->device->getDevices(true);
		foreach ($devices as $device) {
			if ($device == 'desktop') {
				$code = $inline_code['desktop'];
			} else {
				$device_dir = $inline_dir . $this->app->device->getSubdir($device);

				$code = $this->readFromDir($device_dir);
			}

			$code = $this->parseInline($code, $device);
			$code = $this->parse($code);

			$inline_code[$device] = $code;
		}

		//minify the content
		if ($minify) {
			$inline_code = array_map(function ($code) {
				return $this->minify($code);
			}, $inline_code);
		}

		return $inline_code;
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









	/**
	* Merges multiple library files into one
	* @param string $name The name of the library
	* @param string $files The library's files
	* @return string The merged code
	*
	*/
	protected function mergeLibraryFiles(string $name, array $files) : string
	{
		$code = '';
		foreach ($files as $file) {
			$filename = $this->app->libraries_path . App::sl($this->libraries_dir) . App::sl($name) . $file;
			$code.= file_get_contents($filename) . "\n\n";
		}

		return $code;
	}

	/**
	* Merges libraries
	* @param array $libraries The libraries to merge
	* @return string The merged code
	*/
	protected function getLibrariesMerge(array $libraries) : string
	{
		$code = '';
		foreach ($libraries as $library) {
			$code.= file_get_contents($this->base_cache_path . $this->app->cache->getLibraryFile($library, $this->extension)) . "\n\n";
		}

		return $code;
	}

	/**
	* Merges library dependencies
	* @param array $libraries The libraries to merge
	* @return string The merged code
	*/
	protected function getLibraryDependenciesMerge(array $libraries) : string
	{
		$code = '';
		foreach ($libraries as $library) {
			$code.= file_get_contents($this->base_cache_path . $this->app->cache->getLibraryDependencyFile($library, $this->extension)) . "\n\n";
		}

		return $code;
	}
}
