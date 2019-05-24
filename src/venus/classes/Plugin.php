<?php
/**
* The Plugin Class
* @package Venus
*/

namespace Venus;

/**
* The Plugin Class
* Object corresponding to a plugin extension
*/
class Plugin extends \Venus\Extensions\Extension
{
	use \Venus\Extensions\LoadTrait;
	use \Venus\Extensions\LanguagesTrait;
	use \Venus\Extensions\TemplatesTrait;

	/**
	* @var int $pid The plugin's id
	*/
	public $pid = 0;

	/**
	* @var array $hooks Array listing the defined hooks
	*/
	protected $hooks = [];

	/**
	* @internal
	*/
	protected static $id_name = 'pid';

	/**
	* @internal
	*/
	protected static $table = 'venus_plugins';

	/**
	* @internal
	*/
	protected static $type = 'plugin';

	/**
	* @internal
	*/
	protected static $base_dir = 'plugins';

	/**
	* Builds the plugin
	* @param mixed $plugin The plugin's data or id
	*/
	public function __construct($plugin = 0)
	{
		parent::__construct($plugin);

		$this->addHooks();
	}

	/**
	* Adds the hooks
	*/
	protected function addHooks()
	{
		$this->app->plugins->addHooks($this->pid, $this->hooks);
	}
}
