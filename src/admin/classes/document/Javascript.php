<?php
/**
* The Javascript Urls Class
* @package Venus
*/

namespace Venus\Admin\Document;

use Venus\Admin\App;

/**
* The Document's Javascript Urls Class
* Class containing the javascript urls/stylesheets used by a document
*/
class Javascript extends \Venus\Document\Javascript
{
	/**
	* Builds the javascript object
	*/
	public function __construct(App $app)
	{
		parent::__construct($app);

		$this->cache_url = $this->app->admin_cache_url;
	}
}
