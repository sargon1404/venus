<?php
/**
* The Css Urls Class
* @package Venus
*/

namespace Venus\Document;

use Venus\App;

/**
* The Document's Css Urls Class
* Class containing the css urls/stylesheets used by a document
*/
class Css extends \Mars\Document\Css
{
	use \Venus\Assets\CacheTrait;


	/**
	* Builds the css object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		$this->extension = App::FILE_EXTENSIONS['css'];
		$this->base_cache_url = $this->app->cache_url . App::CACHE_DIRS['css'];
		$this->cache_url = $this->base_cache_url;
	}
}
