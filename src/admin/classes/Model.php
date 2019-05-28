<?php
/**
* The Model Class
* @package Venus
*/

namespace Venus\Admin;

/**
* The Model Class
* Implements the Model functionality of the MVC pattern
*/
abstract class Model extends \Venus\Model
{
	/**
	* Returns a created by array
	* @return array
	*/
	public function getCreatedbyArray() : array
	{
		$table = $this->getTable();
		$users_table = $this->getUsersTable();
		$created_by = $this->created_by_name;

		$this->db->readQuery("
			SELECT u.uid, u.username
			FROM {$table} AS c
			LEFT JOIN {$users_table} AS u ON c.{$created_by} = u.uid
			GROUP BY c.{$created_by}");

		return $this->db->getList('uid', 'username');
	}

	/**
	* Returns an array with the defined usergroup, not including the guests
	* @return array
	*/
	public function getUsergroupsArray() : array
	{
		$table = $this->getUsergroupsTable();

		$this->db->readQuery("SELECT ugid, title FROM {$table} WHERE ugid <> " . App::USERGROUPRS['guests'] . " ORDER BY title");

		return $this->db->getList('ugid', 'title');
	}

	/**
	* Returns the categories tree options,ready to be used in a select control
	* @param bool $add_uncategorized If true will add an uncategorized category
	* @return array The categories tree
	*/
	public function getCategoriesTree(bool $add_uncategorized = false) : array
	{
		$table = $this->getCategoriesTable();

		$categories_array = $this->db->selectWithKey($table, 'cid', 'cid, title, level', [], 'position');
		if ($add_uncategorized) {
			$categories_array = [0 => ['cid' => 0, 'title' => l('uncategorized'), 'level' => 0]] + $categories_array;
		}

		return $this->app->tree->create($categories_array, 'title', 'level', '', false);
	}

	/**
	* Returns the ordered items
	* @return array
	*/
	public function getOrderArray() : array
	{
		$table = $this->getTable();
		var_dump("xxxx");
		$this->db->readQuery("SELECT {$this->_id}, {$this->_title}, `{$this->_order}` FROM {$table} ORDER BY `{$this->_order}` DESC");

		return $this->db->get($this->_id);
	}
}
