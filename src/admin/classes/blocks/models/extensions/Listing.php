<?php
/**
* The Listing model for admin blocks managing extensions
* @package Venus
*/

namespace Venus\Admin\Blocks\Models\Extensions;

/**
* The Listing model for admin blocks managing extensions
*/
abstract class Listing extends Base
{
	/**
	* Loads the extenions from the database
	* @param string $filter_sql Optional filter sql
	* @param string $order_sql Optional order sql
	* @param string $fields The list of fields to return. If empty, the default ones will be used
	* @return array Array with the extensions
	*/
	/*public function load($filter_sql = '', $order_sql	 = '', $fields = '')
	{
		global $venus;
		$fields = $this->get_fields();
		$table = $this->get_table();

		$sql = "SELECT {$fields} FROM {$table} {$filter_sql}";

		$items = $this->load_sql($sql);

		$venus->plugins->run($this->prefix . 'load', $this, $items);

		return $items;
	}*/

	/**
	* Returns the list of database fields (columns) to be returned when loading the extensions
	* @return string The list of fields
	*/
	public function get_fields()
	{
		$id_name = $this->get_id_name();

		return "{$id_name}, title, name, status, note, created_by";
	}

	/**
	* Returns the details of an extension (creation_date/created_by etc...)
	* @return array The details
	*/
	public function get_details(int $id)
	{
		$id_name = $this->get_id_name();
		$table = $this->get_table();

		$this->db->read_query("
			SELECT i.created_timestamp, i.created_by, i.modified_timestamp, i.modified_by, c.username as created_by_username, m.username as modified_by_username
			FROM {$table} as i
			LEFT JOIN venus_users AS c ON c.uid = i.created_by
			LEFT JOIN venus_users AS m ON m.uid = i.modified_by
			WHERE {$id_name} = {$id}
		");

		return $this->db->get_row();
	}

	/**
	* Fills an item with the default values for a set operation
	* @return object The item with the default values
	*/
	public function fill_set()
	{
		global $venus;
		$item = $this->get();

		$data = $this->db->fill($this->get_table(), [], '.', '.');

		$item->set_data($data);

		$venus->plugins->run($this->prefix . 'fill_set', $this, $item);

		return $item;
	}

	/**
	* Updates an extensions
	* @param object $item The extension
	* @param array $data The data used to update
	* @return bool True on success, false on failure
	*/
	/*public function update($item, $data = [])
	{
		global $venus;
		$item->bind($data, $this->get_bind_ignore());

		$this->load_installer($item->name, $item);

		$item = $this->process_item($item);

		if (!$item->validate() || !$this->installer->validate($item)) {
			$this->errors->add($item->errors->get());
			return false;
		}

		$id = $item->get_id();

		if (!$item->update()) {
			return false;
		}

		$this->installer->set_id($id);
		$this->installer->update();

		$venus->plugins->run($this->prefix . 'update', $this, $item);

		return true;
	}*/

	/**
	* Updates a set of extensions
	* @param array $ids The ids of the extensions to update
	* @param array $data The data used to update
	* @return bool True on success, false on failure
	*/
	public function update_set($ids, $data)
	{
		global $venus;
		if (!is($ids)) {
			return false;
		}

		$item = $this->get();
		$item->bind($data, $this->get_bind_ignore_set(), '.');

		if (!$this->update_set_validate_item($item)) {
			$this->errors->add($item->errors->get());
			return false;
		}

		$venus->plugins->run($this->prefix . 'update_set', $this, $item, $ids);

		$items = new $this->_class_objs;
		if (!$items->update($item, $ids)) {
			$this->errors->add($items->errors->get());
			return false;
		}

		return true;
	}

	/**
	* Enables extensions
	* @param array $ids The ids of the extensions to enable
	* @return bool True on success, false on failure
	*/
	/*public function enable($ids = [])
	{
		global $venus;
		if (!is($ids)) {
			return false;
		}

		$items = new $this->_class_objs;
		if (!$items->enable($ids)) {
			$this->errors->add($items->errors->get());
			return false;
		}

		$venus->plugins->run($this->prefix . 'enable', $this, $items, $ids);

		return true;
	}*/

	/**
	* Disables extensions
	* @param array $ids The ids of the extensions to disable
	* @return bool True on success, false on failure
	*/
	/*public function disable($ids = [])
	{
		global $venus;
		if (!is($ids)) {
			return false;
		}

		$items = new $this->_class_objs;
		if (!$items->disable($ids)) {
			$this->errors->add($items->errors->get());
			return false;
		}

		$venus->plugins->run($this->prefix . 'disable', $this, $items, $ids);

		return true;
	}
*/
	/**
	* Uninstalls extensions
	* @param array $ids The ids of the extensions to uninstall
	* @return bool True on success, false on failure
	*/
	public function uninstall($ids = [])
	{
		global $venus;
		if (!is($ids)) {
			return false;
		}

		//call the uninstall function of the installer for each item
		$items = $this->db->select_by_ids($this->get_table(), $this->get_id_name(), $ids);
		foreach ($items as $id => $item) {
			$installer = $this->load_installer($item->name, $item);
			if (!$installer->uninstall()) {
				break;
			}
		}

		if (!$venus->ok()) {
			return false;
		}

		$items = new $this->_class_objs;
		if (!$items->uninstall($ids)) {
			$this->errors->add($items->errors->get());
			return false;
		}

		$venus->plugins->run($this->prefix . 'uninstall', $this, $items, $ids);

		return true;
	}
}
