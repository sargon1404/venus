<?php
/**
* The System Plugins Class
* @package Venus
*/

namespace Venus\System;

use Venus\App;

/**
* The System Plugins Class
* Container for the system's plugins
*/
class Plugins extends \Venus\Plugins
{
	use \Mars\Plugins;

	/**
	* @internal
	*/
	protected static $_extensions_table = 'venus_items_plugins';

	/**
	* Builds the plugins object
	* @param App $app The app object
	*/
	public function __construct(App $app)
	{
		$this->app = $app;

		if (!$this->app->config->plugins_enable || defined('DISABLE_PLUGINS')) {
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
			$plugins = $this->app->cache->get('plugins', true);
		} else {
			$table = $this->getTable();

			$this->app->db->readQuery("
				SELECT *
				FROM {$table}
				WHERE status = 1 AND global = 1
				ORDER BY `order` DESC");

			$plugins = $this->app->db->get();

			$this->app->cache->update('plugins', $plugins, true);
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

		$namespace = $this->app->extensions_namespace . static::$namespace;

		foreach ($plugins as $plugin) {
			$pid = $plugin->pid;

			if (isset($this->plugins[$pid])) {
				continue;
			}

			if (!$this->canRun($plugin)) {
				continue;
			}

			$class = $namespace . App::strToClass($plugin->name) . "\\" . App::strToClass($plugin->name);

			$plugin = new $class($plugin);
			var_dump($plugin);
			die;
			if (!$plugin instanceof Plugin) {
				throw new \Exception("Class {$class} must extend class Plugin");
			}

			$this->plugins[$pid] = $plugin;
		}
	}

	/**
	* Determines if the plugin can be run
	* @param object $plugin The plugin
	* @return bool
	*/
	protected function canRun(object $plugin) : bool
	{
		if ($this->app->is_admin) {
			if (!$plugin->admin_access) {
				return false;
			}
		} else {
			if (!$plugin->site_access) {
				return false;
			}
		}

		return true;
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

		$table = $this->getTable();
		$extensions_table = $this->getExtensionsTable();

		$this->app->db->readQuery("
			SELECT p.*
			FROM {$extensions_table} AS pe
			LEFT JOIN {$table} AS p USING(pid)
			WHERE pe.name_crc = CRC32(:name) AND type = :type AND pe.name = :name AND p.status = 1 AND p.global = 0
			ORDER BY p.`order` DESC",
			['type' => $type, 'name' => $name]);

		$plugins = $this->app->db->get();
		if (!$plugins) {
			return false;
		}

		$this->loadPlugins($plugins);

		return true;
	}

	/**
	* Returns a loaded plugin
	* @param int $id The id of the plugin to return
	* @return object The loaded pluing, or null, if nothing is found
	*/
	public function find(int $id) : ?Plugin
	{
		return $this->plugins[$id] ?? null;
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
