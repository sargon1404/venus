<?php
/**
* The Extension's Plugins Trait
* @package Venus
*/

namespace Venus\Extensions;

/**
* The Extension's Plugins Trait
* Trait which allows extensions to load plugins
*/
trait PluginsTrait
{
	/**
	* Determines if the extension has plugins
	* @return bool
	*/
	protected function hasPlugins() : bool
	{
		return $this->app->plugins->loadExtensionPlugins($this->getType(), $this->name);
	}

	/**
	* Loads the extensions's plugins.
	* The protected static $plugins_skip_name property must be set
	*/
	public function loadPlugins()
	{
		if (!$this->app->plugins->enabled) {
			return;
		}

		//is the extension on the list of extensions marked as not having plugins?
		static $skip = null;
		if ($skip === null) {
			$skip = $this->app->cache->get('plugins_extensions_skip', true, []);
		}

		$type = $this->getType();
		if (!empty($skip[$type])) {
			if (in_array($this->name, $skip[$type])) {
				return;
			}
		}

		//mark the extension as not having plugins
		if (!$this->hasPlugins()) {
			if (empty($skip[$type])) {
				$skip[$type] = [];
			}
			if (!in_array($this->name, $skip[$type])) {
				$skip[$type][] = $this->name;

				$this->app->cache->update('plugins_extensions_skip', $skip, true);
			}
		}
	}
}
