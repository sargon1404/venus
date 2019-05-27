<?php
/**
* The Cache Trait
* @package Venus
*/

namespace Venus\Document;

/**
* The Cache Trait
* Provides caching functionality to the object
*/
trait CacheTrait
{
	/**
	* Determines if there is cached data and if the cache data is valid
	* @return bool True if the data is cached&valid, false otherwise
	*/
	protected function isCached() : bool
	{
		if (!$this->cache || !$this->cache_refreshed) {
			return false;
		}
		if ($this->cache_refreshed + $this->cache_interval < time()) {
			return false;
		}

		return true;
	}

	/**
	* Caches the object's content
	*/
	protected function buildCache()
	{
		if (!$this->cache) {
			return;
		}

		$update_array = [
			'cache_refreshed' => $this->app->db->unixTimestamp(),
			'cached' => $this->content
		];

		$this->app->db->updateById($this->getTable(), $update_array, $this->getIdName(), $this->getId());
	}
}
