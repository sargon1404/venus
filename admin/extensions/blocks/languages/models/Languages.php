<?php
/**
* The Admin Languages Models Class
* @package Cms\Admin\Blocks\Languages
*/

namespace Cms\Admin\Blocks\Languages\Models;

/**
* The Admin Languages Models Class
*/
class Languages extends \Venus\Admin\Blocks\Models\Extensions\Listing
{
	use SharedTrait;

	/**
	* @internal
	*/
	protected string $id = 'lid';

	/**
	* @internal
	*/
	protected string $packages_table = 'venus_languages_packages';

	/**
	* @internal
	*/
	protected string $strings_table = 'venus_languages_strings';

	/**
	* @internal
	*/
	public string $prefix = 'admin_block_languages';


	/**
	* Builds the model
	*/
	public function __construct()
	{
		$this->copyStaticProperties($this->properties);

		parent::__construct();
	}

	public function getPackagesTable() : string
	{
		return $this->packages_table;
	}

	public function getStringsTable() : string
	{
		return $this->strings_table;
	}

	public function get_packages($lid)
	{
		$lid = (int)$lid;

		return $this->db->select_list($this->get_packages_table(), 'title', 'pid', "WHERE lid = {$lid}");
	}

	public function get_strings($pid)
	{
		$strings_table = $this->get_strings_table();
		$pid = (int)$pid;

		$this->db->read_query("SELECT * FROM {$strings_table} WHERE pid = {$pid} ORDER BY `order`");

		return $this->db->get();
	}

	/*public function update($item, $data = [])
	{
		global $venus;
		if (!$data['status'] && $venus->config->language_default == $item->lid) {
			$this->errors->add('default_disable');
			return false;
		}

		return parent::update($item, $data);
	}*/

	/*protected function get_bind_ignore()
	{
		return [
			'name', 'packages', 'cached_packages', 'extra_blocks', 'extra_widgets', 'extra_dialogs',
			'created_timestamp', 'created_by'
		];
	}*/

	/*protected function get_bind_ignore_set()
	{
		return [
			'name', 'title', 'code', 'url_code', 'accept_code', 'title_lang', 'site_name',
			'packages', 'cached_packages', 'extra_blocks', 'extra_widgets', 'extra_dialogs',
			'created_timestamp', 'created_by'
		];
	}*/

	public function uninstall($ids = [])
	{
		global $venus;
		//the default language can't be uninstalled
		if (in_array($venus->config->language_default, $ids)) {
			$this->errors->add('default_uninstall');
			return false;
		}

		return parent::uninstall($ids);
	}

	public function set_default($item)
	{
		global $venus;
		$id = $item->lid;

		if (!$item->status) {
			$this->errors->add('disabled_set_default');
			return false;
		}

		$venus->config->set('language_default', $id);

		$venus->cache->clear_language_default();

		$venus->plugins->run($this->prefix . 'set_default', $id);

		return true;
	}

	public function switch_users($item)
	{
		global $venus;
		$id = (int)$item->lid;

		if (!$item->status) {
			$this->errors->add('disabled_switch_users');
			return false;
		}

		$this->db->write_query("UPDATE venus_users SET lang = {$id}");

		$venus->plugins->run($this->prefix . 'switch_users', $id);

		return true;
	}

	public function update_strings($lid, $pid, $strings_array)
	{
		global $venus;
		$strings_table = $this->get_strings_table();
		$lid = (int)$lid;
		$pid = (int)$pid;

		$this->db->write_query("DELETE FROM {$strings_table} WHERE pid = {$pid} AND lid = {$lid}");

		$i = 0;
		foreach ($strings_array as $string => $text) {
			$insert_array =
			[
				'lid' => $lid,
				'pid' => $pid,
				'string' => $string,
				'text' => $text,
				'order' => $i
			];

			$this->db->insert($this->get_strings_table(), $insert_array);
			$i++;
		}

		$venus->cache->clear_language($lid);
		$venus->cache->build_language($lid);

		$venus->plugins->run($this->prefix . 'update_strings', $lid, $pid, $strings_array);
	}
}
