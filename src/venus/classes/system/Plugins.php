<?php
/**
* The System Plugins Class
* @package Venus
*/

namespace Venus\System;

use Venus\App;
use Venus\Plugin;

/**
* The System Plugins Class
* Container for the system's plugins
*/
class Plugins extends \Venus\Plugins
{
	use \Mars\Plugins;

	/**
	* @var string $scope The scope where the plugins will be loaded
	*/
	protected string $scope = 'frontend';

	/**
	* @var string $namespace The namespace used to load plugins
	*/
	protected static string $namespace = "Cms\\Plugins\\";

	/**
	* @internal
	*/
	protected static string $_extensions_table = 'venus_plugins_extensions';

	/**
	* Builds the plugins object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		if (!$this->app->config->plugins_enable) {
			return;
		}
		if (!$this->app->cache->plugins_count) {
			return;
		}

		$this->enabled = true;
	}

	/**
	* Returns the extensions plugins table
	* @return string
	*/
	public function getExtensionsTable() : string
	{
		return static::$_extensions_table;
	}

	/**
	* Loads the plugins
	*/
	public function load(array $where = [], string $order_by = '', string $order = '', int $limit = 0, int $limit_offset = 0, string $fields = '*') : array
	{
		if (!$this->enabled) {
			return [];
		}

		$plugins = [];

		if ($this->app->cache->plugins) {
			$plugins = $this->app->cache->get('plugins', true, []);
		} else {
			$table = $this->getTable();

			$this->app->db->readQuery("
				SELECT *
				FROM {$table}
				WHERE status = 1 AND (scope = :scope or scope = 'both')
				ORDER BY `order` DESC", ['scope' => $this->scope]);

			$plugins = $this->app->db->get();

			$this->app->cache->set('plugins', $plugins, null, true);
		}

		$this->loadPlugins($plugins);

		return $plugins;
	}

	/**
	* Loads plugins
	* @param array Array with the plugin objects to load
	*/
	public function loadPlugins(iterable $plugins)
	{
		if (!$plugins) {
			return;
		}

		foreach ($plugins as $plugin) {
			$class = static::$namespace . App::strToClass($plugin->name) . "\\" . App::strToClass($plugin->name);

			$plugin = new $class($plugin);

			if (!$plugin instanceof Plugin) {
				throw new \Exception("Class {$class} must extend class Plugin");
			}

			$this->plugins[$plugin->name] = $plugin;
		}
	}

	/**
	* Loads the plugins of an extension
	* @param string $type The type of the extension
	* @param string $name The name of the extension
	* @return bool Returns true if plugins have been loaded, false otherwise
	*/
	public function loadExtensionPlugins(string $type, string $name) : bool
	{
		if (!$this->enabled) {
			return false;
		}
		if (!$this->app->cache->plugins_extensions_count) {
			return false;
		}

		$table = $this->getTable();
		$extensions_table = $this->getExtensionsTable();

		$this->app->db->readQuery(
			"
			SELECT p.*
			FROM {$extensions_table} AS pe
			LEFT JOIN {$table} AS p USING(pid)
			WHERE pe.name_crc = CRC32(:name) AND type = :type AND pe.name = :name AND p.status = 1 AND p.global = 0
			ORDER BY p.`order` DESC",
			['type' => $type, 'name' => $name]
		);

		$plugins = $this->app->db->get();
		if (!$plugins) {
			return false;
		}

		$this->loadPlugins($plugins);

		return true;
	}

	/**
	* Returns a loaded plugin
	* @param string $name The name of the plugin to return
	* @return object The loaded pluing, or null, if nothing is found
	*/
	public function findByName(string $name) : ?Plugin
	{
		foreach ($this->plugins as $plugin) {
			if ($plugin->name == $name) {
				return $plugin;
			}
		}

		return null;
	}
}
