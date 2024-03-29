<?php
/**
* The Booter Class
* @package Venus
*/

namespace Venus\Bin;

use Venus\Bin;
use Venus\Admin\System\Language;
use Venus\Admin\System\Plugins;
use Venus\Bin\System\Output;

/**
* The Booter Class
* Initializes the system's required classes
*/
class AppBooter extends \Venus\Admin\AppBooter
{
	/**
	* @see \Mars\Booter::system()
	* {@inheritdoc}
	*/
	public function system()
	{
		$this->app->output = new Output($this->app);

		$this->app->plugins = new Plugins($this->app);
		$this->app->plugins->load();

		$this->app->lang = new Language($this->app);

		$this->app->bin = new Bin($this->app);

		return $this;
	}
}
