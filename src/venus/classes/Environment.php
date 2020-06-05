<?php
/**
* The Environment Class
* @package Venus
*/

namespace Venus;

/**
* The Environment Class
* Returns environment data
*/
class Environment
{
	use AppTrait;

	/**
	* Returns the (cached) usergroups
	* @return array The usergroups
	*/
	public function getUsergroups() : array
	{
		static $usergroups = [];
		if (!$usergroups) {
			$usergroups = $this->app->cache->getUsergroups();
		}

		return $usergroups;
	}

	/**
	* Returns an (cached) usergroup
	* @param int $ugid The usergroup's id
	* @return object The usergroup
	*/
	public function getUsergroup(int $ugid) : ?object
	{
		if (!$ugid) {
			$ugid = APP::USERGROUPS['guests'];
		}

		$usergroup = null;
		$usergroups = $this->getUsergroups();

		if (isset($usergroups[$ugid])) {
			$usergroup = $usergroups[$ugid];
		}

		return $usergroup;
	}

	/**
	* Returns the (cached) categories
	* @return array The categories
	*/
	public function getCategories() : array
	{
		static $categories = [];

		if (!$categories) {
			$categories = $this->app->cache->getCategories();
		}

		return $categories;
	}

	/**
	* Returns the (cached) categories
	* @return array The category ids
	*/
	public function getCategoryIds() : array
	{
		static $categories_ids = [];
		if (!$categories_ids) {
			$categories_ids = $this->app->cache->getCategoryIds();
		}

		return $categories_ids;
	}

	/**
	* Returns a category from the cache
	* @param int $cid The category's id
	* @return object
	*/
	public function getCategory(int $cid) : object
	{
		$category = null;
		$categories = $this->getCategories();

		if (isset($categories[$cid])) {
			$category = $categories[$cid];
		}

		return $category;
	}

	/**
	* Returns the (cached) installed and enabled blocks
	* @return array The blocks
	*/
	public function getBlocks() : array
	{
		static $blocks = [];

		if (!$blocks) {
			$blocks = $this->app->cache->get('blocks', true, []);
		}

		return $blocks;
	}

	/**
	* Returns the (cached) data of a block
	* @param string $name The name of the block
	* @return object The block, or null if it wasn't found
	*/
	public function getBlock(string $name)
	{
		$blocks = $this->getBlocks();

		if (!isset($blocks[$name])) {
			return null;
		}

		return $blocks[$name];
	}

	/**
	* Returns true if a block is installed and enabled
	* @param string $name The name of the block
	* @return bool Returns true if the block is installed & enabled
	*/
	public function isBlock(string $name) : bool
	{
		$block = $this->getBlock($name);
		if (!$block) {
			return false;
		}

		if (!$block->status) {
			return false;
		}

		return true;
	}

	/**
	* Returns the ID of the block
	* @param string $name The name of the block
	* @return int The id of the block, 0 if not found
	*/
	public function getBlockId(string $name) : int
	{
		$block = $this->getBlock($name);
		if (!$block) {
			return 0;
		}

		return $block->id;
	}
}
