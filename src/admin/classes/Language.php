<?php
/**
* The Admin Language Class
* @package Venus
*/

namespace Venus\Admin;

/**
* The Admin Language Class
* The system's admin language extension
*/
class Language extends \Venus\Language
{
	use \Venus\Admin\Extensions\Body;

	/**
	* Builds the admin language object
	* @param string $name The name of the language
	*/
	public function __construct($name = '')
	{
		$this->app = $this->getApp();

		$this->load($name);

		$this->app->plugins->run('admin_language_construct', $this);
	}

	/**
	* Loads the language
	* @param string $name The name of the language
	*/
	public function load($name) : bool
	{
		$this->name = $name;
		$this->title = $name;

		$this->prepare();

		return true;
	}

	/**
	* @see \Venus\Language::prepare()
	* {@inheritdoc}
	*/
	protected function prepare()
	{
		$this->preparePaths();
		$this->prepareParams();
	}

	/**
	* Prepares the language's params, by reading the params file
	*/
	protected function prepareParams()
	{
		$params = [];
		$filename = $this->dir . 'params.php';

		if (is_file($filename)) {
			include($filename);

			$this->assign($params);
		}
	}

	/**
	* @see \Venus\Language::fileExists()
	* {@inheritdoc}
	*/
	public function fileExists(string $filename) : bool
	{
		return is_file($this->dir . $filename);
	}

	/**
	* @see \Venus\Language::parentFileExists()
	* {@inheritdoc}
	*/
	public function parentFileExists(string $filename) : bool
	{
		return is_file($this->parent_dir . $filename);
	}
}
