<?php
/**
* The Main Venus Class
* @package Venus
*/

namespace Venus\Bin;

/**
* The Main Venus Class
* The system's main object
*/
class App extends \Venus\Admin\App
{
	use \Mars\Bin\BinFunctionsTrait;

	/**
	* @var bool $is_bin True if the app is run as a bin script
	*/
	public bool $is_bin = true;

	/**
	* @see \Mars\App::loadBooter()
	* {@inheritdoc}
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
