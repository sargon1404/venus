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

		$filename = $this->app->path . str_replace($site_url, '', $url);

		$this->checkFilename($filename, $this->app->path);

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
	* @see \Mars\File::read()
	* {@inheritdoc}
	*/
	public function read(string $filename)
	{
		$content = parent::read($filename);

		if ($content === false) {
			$this->app->errors->add(App::__('file_read_error', '{FILE}', ['{FILE}' => basename($filename)]));

			return false;
		}

		return $content;
	}

	/**
	* @see \Mars\File::write()
	* {@inheritdoc}
	*/
	public function write(string $filename, string $content, bool $append = false) : bool
	{
		if (!parent::write($filename, $content, $append)) {
			$this->app->errors->add(App::__('file_write_error', '{FILE}', ['{FILE}' => basename($filename)]));

			return false;
		}

		return true;
	}

	/**
	* @see \Mars\File::delete()
	* {@inheritdoc}
	*/
	public function delete(string $filename) : bool
	{
		if (!parent::delete($filename)) {
			$this->app->errors->add(App::__('file_delete_error', ['{FILE}' => basename($filename)]));

			return false;
		}

		return true;
	}

	/**
	* @see \Mars\File::copy()
	* {@inheritdoc}
	*/
	public function copy(string $source, string $destination) : bool
	{
		if (!parent::copy($source, $destination)) {
			$this->app->errors->add(App::__('file_copy_error', ['{FILE}' => basename($source)]));

			return false;
		}

		return true;
	}

	/**
	* @see \Mars\File::move()
	* {@inheritdoc}
	*/
	public function move(string $source, string $destination) : bool
	{
		if (!parent::move($source, $destination)) {
			$this->app->errors->add(App::__('file_move_error', ['{FILE}' => basename($source)]));

			return false;
		}

		return true;
	}
}
