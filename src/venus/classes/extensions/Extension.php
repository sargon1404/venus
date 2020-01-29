<?php
/**
* The Extension Class
* @package Venus
*/

namespace Venus\Extensions;

use Venus\App;

/**
* The Extension Class
* Base class for all extensions
*/
abstract class Extension extends \Mars\Extensions\Extension
{
	/**
	* @var string $title The extension's title
	*/
	public string $title = '';

	/**
	* @var array $params The extension's params
	*/
	public $params = [];

	/**
	* Prepares the extension
	*/
	protected function prepare()
	{
		$this->preparePaths();
		$this->prepareParams();
		$this->prepareDevelopment();
	}

	/**
	* Prepares the extension's params
	*/
	protected function prepareParams()
	{
		$this->params = App::unserialize($this->params);
	}
}
