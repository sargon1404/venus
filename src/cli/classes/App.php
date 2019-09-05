<?php
/**
* The Main Venus Class
* @package Venus
*/

namespace Venus\Cli;

/**
* The Main Venus Class
* The system's main object
*/
class App extends \Venus\App
{
	use \Mars\Cli\CliFunctionsTrait;

	/**
	* @var bool $is_api True if the app is run as as an api call
	*/
	public $is_cli = true;

	/**
	* @see \Mars\App::loadBooter()
	* {@inheritDoc}
	*/
	protected function loadBooter()
	{
		$this->boot = new AppBooter($this);
	}

	/**
	* @see \Venus\App::checkInstalled
	* {@inheritdoc}
	*/
	protected function checkInstalled()
	{
	}

	/**
	* @see \Venus\App::checkOffline
	* {@inheritdoc}
	*/
	public function checkOffline()
	{
	}

}
