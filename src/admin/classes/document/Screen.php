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
		$this->extensions_path = $this->app->extensions_path;

		if (isset($this->app->admin_extensions_path)) {
			$this->extensions_path = $this->app->admin_extensions_path;
		}
	}
}
