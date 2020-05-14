<?php
/**
* The Screen Class
* @package Venus
*/

namespace Venus\Admin\Document;

use Venus\Admin\App;

/**
* The Screen Class
* Contains 'Screen' functionality. Eg: error, message screens etc..
*/
class Screen extends \Venus\Document\Screen
{
	/**
	* Builds the screen object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;
		$this->extensions_dir = $this->app->extensions_dir;

		if (isset($this->app->admin_extensions_dir)) {
			$this->extensions_dir = $this->app->admin_extensions_dir;
		}
	}
}
