<?php
/**
* The Css Generators Class
* @package Venus
*/

namespace Venus\Assets\Generators\Css;

use Venus\App;

/**
* The Css Generators Class
* Class caching css code and generating the urls
*/
class Generators extends \Venus\Assets\Generators
{
	protected array $supported_sources = [
		'\Venus\Assets\Generators\Css\Sources\Theme'
	];

	/**
	* Builds The Css Sources object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		$this->app->plugins->run('assets_generators_css_generators', $this);
	}
}
