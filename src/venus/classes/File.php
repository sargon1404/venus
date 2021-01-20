<?php
/**
* The File Class
* @package Venus
 */

namespace Venus;

/**
* The File Class
* Filesystem functionality
*/
class File extends \Mars\File
{
	/**
	* Returns a filename from a local url
	* @param string $url The url
	* @return string The filename
	*/
	public function getFromUrl(string $url) : string
	{
		if (!$this->app->uri->isLocal($url)) {
			return '';
		}

		$site_url = $this->app->uri->stripScheme($this->app->url);
		$url = $this->app->uri->stripScheme($url);

		$filename = $this->app->dir . str_replace($site_url, '', $url);

		$this->checkFilename($filename, $this->app->dir);

		return $filename;
	}

	/**
	* Will trim the specified filename to $max_length characters by cutting from the middle of the filename
	* @param string $filename The filename which is to be cut, if it's length > $max_length
	* @param int $max_length The max number of characters
	* @param string $replace_with Will replace the removed/cut text with this value
	* @return string The cut filename
	*/
	public function cutFilename(string $filename, int $max_length = 30, string $replace_with = '...') : string
	{
		$filename = $this->app->text->cutMiddle($filename, $max_length, $replace_with);

		$filename = $this->app->plugins->filter('file_cut_filename', $filename, $max_length, $replace_with, $this);

		return $filename;
	}

	/**
	* @see \Mars\File::checkFilename()
	* {@inheritdoc}
	*/
	public function checkFilename(string $filename, string $secure_dir = '')
	{
		if (!$secure_dir) {
			$secure_dir = $this->app->dir;
		}

		parent::checkFilename($filename, $secure_dir);

		return $this;
	}

	/**
	* @see \Mars\File::readFile()
	* {@inheritdoc}
	*/
	public function readFile(string $filename, string $secure_dir = '') : string
	{
		$content = parent::readFile($filename, $secure_dir);

		if ($content === false) {
			$this->app->errors->add(App::__('file_read_error', '{FILE}', ['{FILE}' => basename($filename)]));

			return '';
		}

		return $content;
	}

	/**
	* @see \Mars\File::writeFile()
	* {@inheritdoc}
	*/
	public function writeFile(string $filename, string $content, bool $append = false, string $secure_dir = '') : bool
	{
		$ret = parent::writeFile($filename, $content, $append, $secure_dir);

		if (!$ret) {
			$this->app->errors->add(App::__('file_write_error', '{FILE}', ['{FILE}' => basename($filename)]));

			return false;
		}

		return true;
	}

	/**
	* @see \Mars\File::deleteFile()
	* {@inheritdoc}
	*/
	public function deleteFile(string $filename, string $secure_dir = '') : bool
	{
		$ret = parent::deleteFile($filename, $secure_dir);

		if (!$ret) {
			$this->app->errors->add(App::__('file_delete_error', ['{FILE}' => basename($filename)]));

			return false;
		}

		return true;
	}

	/**
	* @see \Mars\File::copyFile()
	* {@inheritdoc}
	*/
	public function copyFile(string $source, string $destination, string $secure_dir = '') : bool
	{
		$ret = parent::copyFile($source, $destination, $secure_dir);

		if (!$ret) {
			$this->app->errors->add(App::__('file_copy_error', ['{FILE}' => basename($source)]));

			return false;
		}

		return true;
	}

	/**
	* @see \Mars\File::moveFile()
	* {@inheritdoc}
	*/
	public function moveFile(string $source, string $destination, string $secure_dir = '') : bool
	{
		$ret = parent::moveFile($source, $destination, $secure_dir);

		if (!$ret) {
			$this->app->errors->add(App::__('file_move_error', ['{FILE}' => basename($source)]));

			return false;
		}

		return true;
	}

	/**
	* @see \Mars\File::listFile()
	* {@inheritdoc}
	*/
	public function listDir(string $dir, ?array &$dirs, ?array &$files, bool $full_path = false, bool $recursive = false, bool $include_extension = true, array $skip_dirs = [], bool $use_dir_as_file_key = false, bool $is_tree = false, string $tree_prefix = '--', int $tree_level = 0, string $base_dir = '') : bool
	{
		$ret = parent::listDir($dir, $dirs, $files, $full_path, $recursive, $include_extension, $skip_dirs, $use_dir_as_file_key, $is_tree, $tree_prefix, $tree_level, $base_dir);

		if (!$ret) {
			$this->app->errors->add(App::__('dir_open_error', ['{DIR}' => $dir]));

			return false;
		}

		return true;
	}

	/**
	* @see \Mars\File::createDir()
	* {@inheritdoc}
	*/
	public function createDir(string $dir) : bool
	{
		$ret = parent::createDir($dir);

		if (!$ret) {
			$this->app->errors->add(App::__('dir_create_error', ['{DIR}' => $dir]));

			return false;
		}

		return true;
	}

	/**
	* @see \Mars\File::copyDir()
	* {@inheritdoc}
	*/
	public function copyDir(string $source_dir, string $destination_dir, bool $recursive = true) : bool
	{
		$ret = parent::copyDir($source_dir, $destination_dir, $recursive);

		if (!$ret) {
			$this->app->errors->add(App::__('dir_open_error', ['{DIR}' => $dir]));

			return false;
		}

		return true;
	}

	/**
	* @see \Mars\File::moveDir()
	* {@inheritdoc}
	*/
	public function moveDir(string $source_dir, string $destination_dir) : bool
	{
		$ret = parent::moveDir($source_dir, $destination_dir);

		if (!$ret) {
			$this->app->errors->add(App::__('dir_move_error', ['{DIR}' => $dir]));

			return false;
		}

		return true;
	}

	/**
	* @see \Mars\File::deleteDir()
	* {@inheritdoc}
	*/
	public function deleteDir(string $dir, bool $recursive = true, string $secure_dir = '') : bool
	{
		$ret = parent::deleteDir($dir, $recursive, $secure_dir);

		if (!$ret) {
			if ($this->error_code == 1) {
				$this->app->errors->add(App::__('dir_open_error', ['{DIR}' => $dir]));
			} elseif ($this->error_code == 2) {
				$this->app->errors->add(App::__('dir_delete_error', ['{DIR}' => $dir]));
			}

			return false;
		}

		return true;
	}

	/**
	* @see \Mars\File::cleanDir()
	* {@inheritdoc}
	*/
	public function cleanDir(string $dir, bool $recursive = true, string $secure_dir = '') : bool
	{
		$ret = parent::cleanDir($dir, $recursive, $secure_dir);

		if (!$ret) {
			$this->app->errors->add(App::__('dir_open_error', ['{DIR}' => $dir]));

			return false;
		}

		return true;
	}
}
