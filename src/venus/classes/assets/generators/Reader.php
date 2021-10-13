<?php
/**
* The Assets Generators Reader Class
* @package Venus
*/

namespace Venus\Assets\Generators;

use Venus\App;

/**
* The Assets Generators Reader Class
*/
abstract class Reader extends File
{
	/**
	* @var array $skip_dirs Array with the dirs to skip when reading files
	*/
	protected array $skip_dirs = [App::EXTENSIONS_DIRS['inline'], App::EXTENSIONS_DIRS['plugins'], App::MOBILE_DIRS['mobile'], App::MOBILE_DIRS['tablets'], App::MOBILE_DIRS['smartphones']];

	/**
	* Reads content from a dir
	* @param string $dir The folder
	* @param bool $recursive If true, will read the files from $dir recursive
	* @return string The content
	*/
	public function get(string $dir, bool $recursive = true) : string
	{
		$skip_dirs = $this->getDirsToSkip($dir);
		$files = $this->getFiles($dir, $recursive, $skip_dirs);

		return $this->getFromFiles($files);
	}

	/**
	* Returns content from the device dir
	* @param string $device The device
	* @param string $dir The folder from where to read
	* @return string The content
	*/
	public function getForDevice(string $device, string $dir) : string
	{
		if ($device == 'desktop') {
			return '';
		}

		//read content from the mobile dir
		$mobile_dir = $dir . $this->app->device->getSubdir('mobile');
		$content = $this->get($mobile_dir);

		//read the content from the tablets/smartphones dir
		if ($device == 'tablet' || $device == 'smartphone') {
			$device_dir = $dir . $this->app->device->getSubdir($device);

			$content.= $this->get($device_dir);
		}

		return $content;
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

		$files_array = $this->app->dir->getFiles($dir, $recursive, $full_path, $skip_dirs, [$this->extension], true);
		if (!$files_array) {
			return [];
		}


		$files = [];
		foreach ($files_array as $dir => $files_list) {
			$desktop_files = [];
			$mobile_files = [];

			//split the files into desktop and mobile
			foreach ($files_list as $filename) {
				if (str_contains($filename, 'mobile')) {
					$mobile_files[] = $filename;
				} else {
					$desktop_files[] = $filename;
				}
			}

			natsort($desktop_files);
			natsort($mobile_files);

			$files_list = array_merge($desktop_files, $mobile_files);

			$files = array_merge($files, $files_list);
		}

		return $files;
	}

	/**
	* Returns the code from a list of files
	* @param array $files The files to load the code from
	* @param string $dir If specified will prefix each filename with the dir name
	* @return string The combined code
	*/
	public function getFromFiles(array $files, string $dir = '') : string
	{
		if (!$files) {
			return '';
		}

		$content = '';
		foreach ($files as $file) {
			$file_cnt = file_get_contents($dir . $file);

			$content.= $file_cnt . "\n\n";
		}

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
}
