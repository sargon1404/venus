<?php
/**
* The Export Class
* @package Venus
*/

namespace Venus\Admin\Helpers;

use Venus\Admin\App;

class Export
{
	use \Venus\AppTrait;

	public string $header_file = 'header.json';

	protected array $header = [];

	protected string $zip_dir = '';

	protected array $dirs = [];

	protected array $files = [];

	protected object $handle;

	public function __construct()
	{
		if (!extension_loaded('zlib')) {
			throw new \Exception('You must have the zlib extension installed in order to use the export functionality.');
		}

		$this->app = $this->getApp();

		$this->handle = new \ZipArchive;
	}

	/**
	* Sets the archive header
	* @param array $header The custom header of the archive. Can be retrieved when unziping it
	* @return $this
	*/
	public function setHeader($header)
	{
		$this->header = $header;

		return $this;
	}

	/**
	* Returns the custom header data
	* @param array The custom header
	*/
	public function getHeader()
	{
		return $this->header;
	}

	/**
	* Adds a dir to the archive file
	* @param string $dir The name of the dir
	* @param bool $recursive If true, will archive the dir recursivly
	* @return $this
	*/
	public function addDir($dir, $recursive = true)
	{
		$this->dirs[] = [$dir, $recursive];

		return $this;
	}

	/**
	* Adds a file to the archive file
	* @return $this
	*/
	public function addFile($file)
	{
		$this->files[] = $file;

		return $this;
	}

	/**
	* Outputs the generated archive for download
	* @param string $filename The name under which the file will be saved
	* @param bool $use_header If set to true will also include the user generated header - set by set_header in the zip file
	* @return bool Returns true on success, false on failure. Die should be called after this function
	*/
	public function promptForDownload($filename, $use_header = true)
	{
		if (!$this->handle) {
			return false;
		}

		$tmpfile = tempnam($this->app->temp_dir, 'venus_export');

		$ret = $this->createZip($tmpfile, $use_header);
		if (!$ret) {
			unlink($tmpfile);
			return false;
		}

		if ($this->app->ok()) {
			$ret = $this->app->file->promptForDownload($tmpfile, $filename . '.zip');
			$this->app->file->delete($tmpfile);

			if (!$ret) {
				return false;
			}
		} else {
			unlink($tmpfile);
			return false;
		}

		return true;
	}

	/*
	* Creates the zip archive
	*/
	protected function createZip($filename, $use_header = false)
	{
		if (!$this->dirs && !$this->files) {
			return false;
		}

		try {
			if ($this->handle->open($filename, \ZIPARCHIVE::CREATE) !== true) {
				throw new \Exception(l('export_error1'));
			}

			foreach ($this->dirs as $params) {
				$dir = App::sl($params[0]);
				$extrapath = '';
				if (count($this->dirs) > 1) {
					$extrapath = basename($dir) . '/';
				}

				$dirs = [];
				$files = [];

				$this->app->file->listDir($dir, $dirs, $files, true, $params[1]);

				if ($files) {
					$zip_files = [];
					foreach ($files as $file) {
						$zip_file = str_replace($dir, '', $file);
						$zip_files[$zip_file] = $file;
					}

					foreach ($zip_files as $zip_file => $filename) {
						if (!is_readable($filename)) {
							$this->handle->close();

							throw new \Exception(l('export_error2', '{FILENAME}', $filename));
						}
						$this->handle->addFile($filename, $extrapath . $zip_file);
					}
				}
			}
			if ($this->files) {
				foreach ($this->files as $filename) {
					if (!is_readable($filename)) {
						$this->handle->close();

						throw new \Exception(l('export_error2', '{FILENAME}', $filename));
					}

					$this->handle->addFile($filename, basename($filename));
				}
			}
			if ($use_header) {
				$this->handle->addFromString($this->header_file, encode($this->header));
			}

			$this->handle->close();
		} catch (\Exception $e) {
			$this->app->errors->add($e->getMessage());

			return false;
		}

		return true;
	}

	/**
	* Loads the custom header
	*/
	protected function loadHeader()
	{
		$header_file = App::sl($this->zip_dir) . $this->header_file;
		if (!is_file($header_file)) {
			return;
		}

		$this->header = App::decode($this->app->file->read($header_file));
	}

	/**
	* Unzips the $filename archive to a temp dir
	* @param string $filename The filename of the archive
	* @param string $unzip_dir The folder where the file should be unziped. If empty, it will be unziped to the temp folder
	* @return array Returns the custom header, if any. Additional checking can be done based on the header, and then decide if the unziped contents are deleted or moved to another location
	*/
	public function unzip($filename, $unzip_dir = '')
	{
		if (!$this->handle || !$filename) {
			return [];
		}

		if ($unzip_dir) {
			$this->zip_dir = $unzip_dir;
		} else {
			$this->zip_dir = tempnam($this->app->temp_dir, 'venus_export');

			unlink($this->zip_dir);
		}

		if (!$this->app->dir->create($this->zip_dir)) {
			return false;
		}
		if (!$this->handle->open($filename)) {
			return false;
		}
		if (!$this->handle->extractTo($this->zip_dir)) {
			return false;
		}

		$this->loadHeader();
		$this->handle->close();

		return $this->header;
	}

	/**
	* Moves the archive to $destination_dir
	* @param string $destination_dir The destination dir
	* @return bool True on success, false on failure
	*/
	public function processZip($destination_dir)
	{
		if (!$this->zip_dir) {
			return;
		}

		$this->zip_dir = App::sl($this->zip_dir);

		$this->app->file->delete($this->zip_dir . $this->header_file);

		if (!$this->app->dir->create($destination_dir)) {
			$this->deleteZipDir();
			return false;
		}

		$return = $this->app->dir->copy($this->zip_dir, $destination_dir);

		$this->deleteZipDir();

		return $return;
	}

	/**
	* Deletes the archive file
	* @return $this
	*/
	public function deleteZipDir()
	{
		$this->app->dir->delete($this->zip_dir);

		return $this;
	}
}
