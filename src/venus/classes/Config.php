<?php
/**
* The Config Class
* @package Venus
*/

namespace Venus;

/**
* The Config Class
* Stores the system's config options
*/
class Config extends \Mars\Config
{
	/**
	* @var string $table The database table used to store the config settings
	*/
	protected $table = 'venus_config';

	/**
	* @var string $key The memcache key used to store the config settings data, if any
	*/
	protected $key = 'venus_config';

	/**
	* Builds the config object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$defaults = [
			'admin_dir' => 'admin',
			'session_table' => 'venus_sessions'
		];

		parent::__construct($app);

		$this->defaults = array_merge($this->defaults, $defaults);
	}

	/**
	* @see \Mars\Config::normalize()
	*/
	protected function normalize()
	{
		//start the session & device & libraries iregardless of any config settings
		$this->session_start = true;
		$this->device_start = true;
		$this->libraries_start = true;

		if ($this->debug_ips) {
			$this->debug_ips = explode(',', $this->debug_ips);
		} else {
			$this->debug_ips = [];
		}

		$this->admin_dir = App::sl($this->admin_dir);

		parent::normalize();
	}
}
