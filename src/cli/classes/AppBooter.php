<?php
/**
* The Booter Class
* @package Venus
*/

namespace Venus\Cli;

use Mars\Cli;
use Venus\System\Plugins;
use Venus\Cli\System\{Output, Language};

/**
* The Booter Class
* Initializes the system's required classes
*/
class AppBooter extends \Venus\AppBooter
{
	/**
	* @see \Mars\Booter::system()
	* {@inheritDoc}
	*/
	public function system()
	{
		$this->app->output = new Output($this->app);
		$this->app->plugins = new Plugins($this->app);
		$this->app->plugins->load();

		$this->app->lang = new Language($this->app);
		
		$this->app->cli = new Cli;

		return $this;
	}
}
