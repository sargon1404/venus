<?php
/**
* The Css Urls Class
* @package Venus
*/

namespace Venus\Admin\Document;

use Venus\Admin\App;

/**
* The Document's Css Urls Class
* Class containing the css urls/stylesheets used by a document
*/
class Css extends \Venus\Document\Css
{
	/**
	* Builds the css object
	*/
	public function __construct(App $app)
	{
		parent::__construct($app);

		$this->cache_url = $this->app->admin_cache_url;
	}
}
