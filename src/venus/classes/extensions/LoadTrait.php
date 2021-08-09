<?php
/**
* The Extension's Load Trait
* @package Venus
*/

namespace Venus\Extensions;

use venus\App;

/**
* The Extension's Load Trait
* Trait which allows extensions to load assets
*/
trait LoadTrait
{
	/**
	* Loads a css file from the extension's css dir
	* @param string $file The name of the file to load (must not include the .css extension)
	* @param string $name The name of the extension from where to load the file. If empty, the current extension is used
	* @return $this
	*/
	public function loadCss(string $file, string $name = '')
	{
		if (!$name) {
			$name = $this->name;
		}

		$filename = $this->getPathUrl($name) . App::EXTENSIONS_DIRS['css'] . $file . '.css';

		$this->app->css->load($filename);

		return $this;
	}

	/**
	* Loads a javascript file from the extension's javascript dir
	* @param string $file The name of the file to load (must not include the .js extension)
	* @param string $name The name of the extension from where to load the file. If empty, the current extension is used
	* @return $this
	*/
	public function loadJavascript(string $file, string $name = '')
	{
		if (!$name) {
			$name = $this->name;
		}

		$filename = $this->getPathUrl($name) . App::EXTENSIONS_DIRS['javascript'] . $file . '.js';

		$this->app->javascript->load($filename);

		return $this;
	}

	/**
	* Loads a file from the extension's functions dir
	* @param string $file The name of the file to load (must not include the .php extension)
	* @param string $name The name of the extension from where to load the file. If empty, the current extension is used
	* @return $this
	*/
	public function loadFunctions(string $file, string $name = '')
	{
		if (!$name) {
			$name = $this->name;
		}

		$filename = $this->getPath($name) . App::EXTENSIONS_DIRS['functions'] . $functions_file . '.php';

		require_once($filename);

		return $this;
	}

	/**
	* Loads a file from the extension's objects dir
	* @param string $file The name of the file to load (must not include the .php extension)
	* @param string $name The name of the extension from where to load the file. If empty, the current extension is used
	* @return $this
	*/
	public function loadObject(string $file, string $name = '')
	{
		if (!$name) {
			$name = $this->name;
		}

		$filename = $this->getPath($name) . App::EXTENSIONS_DIRS['objects'] . $file . '.php';

		require_once($filename);

		return $this;
	}

	/**
	* Alias for load_object
	* @param string $file The name of the file to load (must not include the .php extension)
	* @param string $name The name of the extension from where to load the file. If empty, the current extension is used
	* @return $this
	*/
	public function loadObjects(string $file = '', string $name = '')
	{
		$this->loadObject($file, $name);

		return $this;
	}
}
