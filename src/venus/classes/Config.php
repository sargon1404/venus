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
	protected string $table = 'venus_config';

	/**
	* @var string $key The memcache key used to store the config settings data, if any
	*/
	protected string $key = 'venus_config';

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
	* Reads the config files
	* @return $this
	*/
	public function read()
	{
		parent::read();

		$this->readFilename(App::DIRS['cache'] . '/config.php');

		return $this;
	}

	/**
	* @see \Mars\Data::load()
	*/
	public function load()
	{
		parent::load();

		$this->normalizeAfterLoad();
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

		$this->admin_dir = App::sl($this->admin_dir);

		parent::normalize();
	}

	protected function normalizeAfterLoad()
	{
		if ($this->debug_ips) {
			$this->debug_ips = explode(',', $this->debug_ips);
		} else {
			$this->debug_ips = [];
		}

		if ($this->debug_ips) {
			if (in_array($_SERVER['REMOTE_ADDR'], $this->debug_ips)) {
				$this->debug = true;
			}
		}

		if ($this->debug) {
			$this->db_debug = true;
		}
	}
}
