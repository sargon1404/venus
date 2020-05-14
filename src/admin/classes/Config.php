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
	* @internal
	*/
	protected array $read_scope = ['frontend', 'admin'];

	/**
	* @internal
	*/
	protected string $write_scope = 'admin';

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
