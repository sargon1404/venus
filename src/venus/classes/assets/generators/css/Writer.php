<?php
/**
* The Css Assets Writer Class
* @package Venus
*/

namespace Venus\Assets\Generators\Css;

use Venus\App;
use Mars\Minifiers\Css as Minifier;

/**
* The Css Assets Writer Class
*/
class Writer extends \Venus\Assets\Generators\Writer
{
	/**
	* Builds the css asset writer object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		$this->extension = App::FILE_EXTENSIONS['css'];
		$this->cache_path = $this->app->cache_path . App::CACHE_DIRS['css'];
		$this->minify = $this->app->config->getFromScope('css_minify', 'frontend');
		$this->minifier = new Minifier;
	}
}
