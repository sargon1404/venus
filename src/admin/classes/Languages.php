<?php
/**
* The Admin Languages Class
* @package Venus
*/

namespace Venus\Admin;

/**
* The Languages Class
* Container for multiple languages
*/
class Languages extends \Venus\Entities
{
	use \Venus\AppTrait;

	/**
	* @internal
	*/
	protected static $class = '\Venus\Admin\Language';

	/**
	* @internal
	*/
	protected static $base_dir = 'languages';

	/**
	* @internal
	*/
	public function load() : array
	{
		$this->app->file->listDir($this->app->admin_dir . App::DIRS['extensions'] . '/' . static::$base_dir, $dirs, $files);

		$languages = [];
		foreach ($dirs as $dir) {
			$languages[] = $this->getObject($dir);
		}

		$this->setData($languages, false);

		return $languages;
	}
}
