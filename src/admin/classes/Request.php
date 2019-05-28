<?php
/**
* The Request Class
* @package Venus
*/

namespace Venus\Admin;

/**
* The Request Class
*/
class Request extends \Venus\Request
{
	/**
	* Builds the admin request object
	*/
	public function __construct(App $app)
	{
		parent::__construct($app);

		$this->cookie_expires = 0;
		$this->cookie_path = $this->app->config->cookie_path . $this->app->config->admin_dir;
		$this->cookie_domain = $this->app->config->cookie_domain;
	}
}
