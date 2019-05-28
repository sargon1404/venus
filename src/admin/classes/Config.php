<?php
/**
* The Config Class
* @package Venus
*/

namespace Venus\Admin;

/**
* The Config Class
* Stores the system's config options
*/
class Config extends \Venus\Config
{
	/**
	* Builds the config object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$defaults = [
			//'cookie_path' => null,
			'cookie_domain' => '',

			'lang' => 'english',
			'theme' => 'venus'
		];

		parent::__construct($app);

		$this->defaults = array_merge($this->defaults, $defaults);
	}
}
