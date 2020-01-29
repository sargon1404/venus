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
	public int $pid = 0;

	/**
	* @var array $hooks Array listing the defined hooks
	*/
	protected array $hooks = [];

	/**
	* @internal
	*/
	protected static string $id_name = 'pid';

	/**
	* @internal
	*/
	protected static string $table = 'venus_plugins';

	/**
	* @internal
	*/
	protected static string $type = 'plugin';

	/**
	* @internal
	*/
	protected static string $base_dir = 'plugins';

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
