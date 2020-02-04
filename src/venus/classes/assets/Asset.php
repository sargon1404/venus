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
	use CacheTrait;

	/**
	* @var array $skip_dirs Array with the dirs to skip when reading files
	*/
	protected array $skip_dirs = [App::EXTENSIONS_DIRS['inline'], App::EXTENSIONS_DIRS['plugins'], App::MOBILE_DIRS['mobile'], App::MOBILE_DIRS['tablets'], App::MOBILE_DIRS['smartphones']];

	/**
	* @var string $dir The dir from where the assets will be loaded
	*/
	protected string $dir = '';

	/**
	* @var string $extension The extension of the files for this type of asset
	*/
	protected string $extension = '';

	/**
	* @var string $base_cache_dir The folder where the assets will be cached, for the frontend
	*/
	protected string $base_cache_dir = '';

	/**
	* @var string $cache_dir The folder where this assets will be cached
	*/
	protected string $cache_dir = '';

	/**
	* @var string $libraries_dir The folder where the libraries of this type are located
	*/
	protected string $libraries_dir = '';

	/**
	* @var bool $minify True, if the output can be minified
	*/
	protected bool $minify = true;

	/**
	* Returns the asset responsible for handling the dependencies for libraries of this type
	* @return Asset The asset handling the dependencies
	*/
	abstract public function getDependenciesHandler() : Asset;

	/**
	* Reads content from a dir
	* @param string $dir The folder
	* @return string The content
	*/
	protected function readFromDir(string $dir) : string
	{
		return $this->get($dir, true, $this->getDirsToSkip($dir));
	}

	/**
	* Returns content from the device dir
	* @param string $dir The folder
	* @param string $device The device
	* @return string The content
	*/
	protected function readFromDeviceDir(string $dir, string $device) : string
	{
		if ($device == 'desktop') {
			return '';
		}

		//read content from the mobile dir
		$mobile_dir = $dir . $this->app->device->getSubdir('mobile');
		$content = $this->get($mobile_dir, true, $this->getDirsToSkip($mobile_dir));

		//read the content from the tablets/smartphones dir
		if ($device == 'tablet' || $device == 'smartphone') {
			$device_dir = $dir . $this->app->device->getSubdir($device);

			$content.= $this->get($device_dir, true, $this->getDirsToSkip($device_dir));
		}

		return $content;
	}

	/**
	* Returns the code from the files found in a folder
	* @param string $dir The folder to read
	* @param bool $recursive If true, will read the files from $dir recursive
	* @param array $skip_dirs Array with the dirs to skip if $recursive = true
	* @return string The combined code
	*/
	public function get(string $dir, bool $recursive = true, array $skip_dirs = []) : string
	{
		$files_array = $this->getFiles($dir, $recursive, $skip_dirs);
		if (!$files_array) {
			return '';
		}

		return $this->getFromFiles($files_array);
	}

	/**
	* Returns the files list found in a folder
	* @param string $dir The folder to read
	* @param bool $recursive If true, will read the files from $dir recursive
	* @param array $skip_dirs Array with the dirs to skip if $recursive = true
	* @param bool If true, will return the full path of the files
	* @return array The files list
	*/
	protected function getFiles(string $dir, bool $recursive = true, array $skip_dirs = [], bool $full_path = true) : array
	{
		if (!is_dir($dir)) {
			return [];
		}

		$this->app->file->listDir($dir, $dirs, $files, $full_path, $recursive, true, $skip_dirs, true);
		if (!$files) {
			return [];
		}

		$files_array = [];
		foreach ($files as $dir => $files_list) {
			natsort($files_list);

			foreach ($files_list as $filename) {
				$ext = $this->app->file->getExtension($filename);
				if ($ext != $this->extension) {
					continue;
				}

				$files_array[] = $filename;
			}
		}

		return $files_array;
	}

	/**
	* Returns the code from a list of files
	* @param array $files The files to load the code from
	* @param string $dir If specified will prefix each filename with the dir name
	* @return string The combined code
	*/
	public function getFromFiles(array $files, string $dir = '') : string
	{
		$content = '';
		foreach ($files as $file) {
			$file_cnt = file_get_contents($dir . $file);

			$content.= $file_cnt . "\n\n";
		}

		return $content;
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
		return $this->storeFile($this->cache_dir, $file, $content, false, $minify, $parse);
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
		return $this->storeFile($this->app->cache_dir . App::CACHE_DIRS['libraries'], $file, $content, false, $minify, false);
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
		$filename = $this->cache_dir . $file;
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

			$this->storeFile($this->cache_dir, $file, $content, true, $file_minify, $file_parse, $file_parse_params);
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
	* Returns a list with the dirs to skip when building the asset
	* @param string $dir The dir
	* @return array The dirs list
	*/
	protected function getDirsToSkip(string $dir) : array
	{
		$dirs = [];
		foreach ($this->skip_dirs as $skip_dir) {
			$dirs[] = $dir . $skip_dir;
		}

		return $dirs;
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
			$filename = $this->app->libraries_dir . App::sl($this->libraries_dir) . App::sl($name) . $file;
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
			$code.= file_get_contents($this->base_cache_dir . $this->app->cache->getLibraryFile($library, $this->extension)) . "\n\n";
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
			$code.= file_get_contents($this->base_cache_dir . $this->app->cache->getLibraryDependencyFile($library, $this->extension)) . "\n\n";
		}

		return $code;
	}
}
