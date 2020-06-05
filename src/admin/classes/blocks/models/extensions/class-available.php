<?php
/**
* The Available Model Class
* @author Venus-CMS
* @package Venus
*/

namespace venus\admin\blocks\models\extensions;

use function venus\e;
use function venus\ue;
use function venus\eid;
use function venus\ee;
use function venus\l;
use function venus\el;
use function venus\ejs;
use function venus\ejsc;
use function venus\ejsl;
use function venus\sl;
use function venus\usl;
use function venus\is;
use function venus\to_array;
use function venus\to_item;

use venus\Item;

if (!defined('VENUS')) {
	die;
}


/**
* The Available Model Class
* Model used to display the 'available' screen for extension blocks
*/
abstract class Available extends Base
{

	/**
	* Checks if the extension is actually on disk
	* @param $name The name of the extension
	* @return bool
	*/
	public function is_on_disk(string $name) : bool
	{
		return is_dir($this->dir . $name);
	}

	/**
	* Checks if the extension isn't installed
	* @param $name The name of the extension
	* @return bool
	*/
	public function is_installed(string $name) : bool
	{
		global $venus;
		if ($this->db->count($this->get_table(), ['name' => $name])) {
			return true;
		}

		return false;
	}

	/**
	* Returns all the available items
	* @param array $where Where conditions in the format col => val
	* @param string $order_by The order by column
	* @param string $order The order: asc/desc
	* @param int $limit The limit
	* @param int $limit_offset The limit offset, if any
	* @param string $fields The fields to select
	* @return array The items
	*/
	public function get_all(array $where = [], string $order_by = '', string $order = '', int $limit = 0, int $limit_offset = 0, string $fields = '*') : iterable
	{
		global $venus;
		$installed_items = $this->db->select_array($this->get_table(), 'name');

		$venus->file->list_dir($this->get_root_dir(), $available_items, $files);
		$available_items = array_diff($available_items, to_array($installed_items));

		natsort($available_items);

		$i = 0;
		$items = [];

		//load the info for each item
		foreach ($available_items as $name) {
			$info = $this->get_info($name)->get();

			$item = new Item($info);
			$item->name = $name;

			$items[] = $item;
		}

		$venus->plugins->run($this->prefix . 'get_all', $items, $this);

		return $items;
	}












	/**
	* Fills the item with data
	* @param string $name The name of the extension
	* @return object The filled object
	*/
	public function fill($name)
	{
		global $venus;
		$vars = $this->installer->get_vars();
		$info = $this->get_info($name)->get();

		$item = $this->get(null);

		$item->name = $name;

		$data = $this->get_fill_data($name, $vars);

		$item->set_data($data);

		$item->title = $info['title'];
		$item->info = $info;

		$venus->plugins->run($this->prefix . 'fill', $this, $item);

		return $item;
	}

	/**
	* The data to fill the extension with
	*/
	protected function get_fill_data($name, $vars)
	{
		return $this->db->fill($this->get_table(), ['name' => $name, 'status' => 1, 'note' => '', 'debug' => 0], -1);
	}

	/**
	* Inserts the extension in the db
	* @param string $name The name of the extension
	* @param array $data The data to use
	* @return The id of the newly inserted item, or false on failure
	*/
	public function insert($name, $data = [])
	{
		global $venus;
		$item = $this->get(null);

		$item->bind($data);

		$this->load_installer($name, $item);

		$item->name = $name;

		$item = $this->process_item($item);

		if (!$item->validate() || !$this->installer->validate($item)) {
			$this->errors->add($item->errors->get());
			return false;
		}

		$id = $item->insert();
		if (!$id) {
			$this->errors->add($item->errors->get());
			return false;
		}

		$venus->plugins->run($this->prefix . 'insert', $this, $item, $id);

		$this->installer->id = $id;
		$this->installer->insert();

		$venus->cache->build_language($id);

		return $id;
	}

	/**
	* Delete the extensions with the names in the $names array from the disk
	* @param array $names The names of the extensions to delete
	* @return bool True on success, false on failure
	*/
	public function delete($names = []) : int
	{
		global $venus;
		if (!is($names)) {
			return false;
		}
		if (!is_iterable($names)) {
			$names = [$names];
		}

		foreach ($names as $name) {
			if (!$this->is_on_disk($name)) {
				return false;
			}
			if ($this->is_installed($name)) {
				return false;
			}
		}

		$venus->plugins->run($this->prefix . 'delete1', $this, $names);

		//load the installer for each extension and execute the delete method
		foreach ($names as $name) {
			$installer = $this->load_installer($name);
			if (!$installer->delete()) {
				break;
			}
		}

		if (!$venus->ok()) {
			return false;
		}

		$venus->plugins->run($this->prefix . 'delete2', $this, $names);

		foreach ($names as $name) {
			//if(!$venus->file->delete_dir($this->dir . $name))
				//return false;
		}

		$venus->plugins->run($this->prefix . 'delete3', $this, $names);

		return true;
	}
}
