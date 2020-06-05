<?php
namespace venus\admin\admin_languages;

use function venus\_is;
use function venus\_isset;
use function venus\_ifset;
use function venus\_if;
use function venus\_ifc;
use function venus\_get;

if (!defined('VENUS')) {
	die;
}


class Languages extends \venus\admin\objects\Extensions
{
	protected $_id = 'lid';
	protected $_table = 'venus_languages';

	protected $_packages_table = 'venus_languages_packages';
	protected $_strings_table = 'venus_languages_strings';
	protected $_uninstalled_table = 'venus_languages_uninstalled';

	protected $prefix = 'admin_languages_objs_';


	public function get_packages_table()
	{
		return $this->_packages_table;
	}

	public function get_strings_table()
	{
		return $this->_strings_table;
	}

	protected function get_uninstalled_table()
	{
		return $this->_uninstalled_table;
	}

	/**
	/**
	* Disables the languages matching the ids
	* @param array $ids The ids of the languages to disable
	*/
	public function disable($ids = [])
	{
		global $venus;
		$ids = $this->get_ids($ids);
		if (!is($ids)) {
			return false;
		}

		if (in_array($venus->config->language_default, $ids)) {
			$this->errors->add('default_disable');
			return false;
		}

		return parent::disable($ids);
	}

	/**
	* Updates multiple languages based on $item
	* @param object $item The base language
	* @param array $ids The ids of the languages to update
	*/
	public function update($item, $ids = [])
	{
		global $venus;
		$ids = $this->get_ids($ids);
		if (!is($ids)) {
			return false;
		}

		if (isset($item->status)) {
			if (!$item->status && in_array($venus->config->language_default, $ids)) {
				$this->errors->add('default_disable');
				return false;
			}
		}

		return parent::update($item, $ids);
	}

	/**
	* Uninstalls the languages matching the ids
	* @param array $ids The ids of the languages to uninstall
	*/
	public function uninstall($ids = [])
	{
		global $venus;
		$ids = $this->get_ids($ids);
		if (!is($ids)) {
			return false;
		}

		if (in_array($venus->config->language_default, $ids)) {
			$this->errors->add('default_uninstall');
			return false;
		}

		$venus->plugins->run($this->prefix . 'uninstall1', $ids);

		//set the languages as uninstalled in the uninstalled table
		$uninstalled_inserts_array = [];
		$languages = $this->db->select_by_ids($this->get_table(), 'lid', $ids);
		foreach ($languages as $lid => $language) {
			$uninstalled_inserts_array[] = [$language->name, (int)$lid];
		}

		$this->db->insert_multiple($this->get_uninstalled_table(), $uninstalled_inserts_array);

		//delete the entries from the tables
		$this->db->delete_by_ids($this->get_table(), 'lid', $ids);
		$this->db->delete_by_ids($this->get_packages_table(), 'lid', $ids);
		$this->db->delete_by_ids($this->get_strings_table(), 'lid', $ids);

		//reset the language of the users using this lang. to the default one
		$in = $this->db->get_in($ids);
		$default_lang = (int)$venus->config->language_default;

		$this->db->write_query("UPDATE venus_users SET lang = {$default_lang} WHERE lang IN({$in})");

		///clear the cache for each language
		foreach ($ids as $lid) {
			$venus->cache->clear_language($lid);
		}

		$venus->plugins->run($this->prefix . 'uninstall2', $ids);

		return true;
	}
}
