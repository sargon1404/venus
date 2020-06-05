<?php
namespace venus\admin\admin_languages;

use function venus\_e;
use function venus\_l;
use function venus\_s;
use function venus\_el;
use function venus\_ee;
use function venus\_ejs;
use function venus\_ejsc;
use function venus\_slash;
use function venus\{serialize_field};

if (!defined('VENUS')) {
	die;
}


class Language extends \venus\admin\objects\Extension
{
	public $is_default = false;

	protected static $_id = 'lid';
	protected static $_table = 'venus_languages';

	protected $_packages_table = 'venus_languages_packages';
	protected $_strings_table = 'venus_languages_strings';
	protected $_uninstalled_table = 'venus_languages_uninstalled';

	protected $packages_array = [];

	protected $dir = VENUS_LANGUAGES_DIR;

	protected static $_ignore_data = ['info', 'is_default'];

	protected $prefix = 'admin_languages_obj_';


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

	public function set_packages($packages_array)
	{
		$this->packages_array = $packages_array;
	}

	/**
	* Prepares the language
	*/
	public function prepare()
	{
		global $venus;
		$this->is_default = false;
		if ($venus->config->language_default == $this->lid) {
			$this->is_default = true;
		}

		parent::prepare();
	}

	/**
	* Processes the language
	*/
	public function process()
	{
		$this->extra_blocks = serialize_field($this->get_extra_blocks());
		$this->extra_widgets = serialize_field($this->get_extra_widgets());
		$this->extra_dialogs = serialize_field($this->get_extra_dialogs());
	}

	/**
	* Inserts the language in the database
	*/
	public function insert()
	{
		$lid = parent::insert();
		if (!$lid) {
			return 0;
		}

		//was the language previously installed then uninstalled. If yes, preserve the old ID
		$old_language = $this->db->select_row($this->get_uninstalled_table(), '*', ['name' => $this->name]);
		if ($old_language) {
			$this->db->update_by_id($this->get_table(), ['lid' => $old_language->lid], 'lid', $lid);

			//delete the entry from the uninstalled table
			$this->db->delete($this->get_uninstalled_table(), ['name' => $this->name]);

			$this->lid = $lid = $old_language->lid;
		}

		$this->insert_packages();

		return $lid;
	}

	protected function insert_packages()
	{
		if (!$this->packages_array) {
			return;
		}

		$packages_array = [];

		$dummy = new \venus\helpers\Dummy;
		foreach ($this->packages_array as $package_name => $package_title) {
			//insert the package
			$pid = $this->insert_package($package_name, $package_title);

			$packages_array[$pid] = $package_name;

			//insert the strings
			$file = $this->dir . sl($this->name) . $package_name . VENUS_LANGUAGES_FILES_EXTENSION;
			$dummy->strings = [];
			$dummy->load($file);

			if (!$dummy->strings) {
				continue;
			}

			$this->insert_strings($pid, $dummy->strings);
		}

		//update the db
		if ($packages_array) {
			$table = $this->get_table();
			$lid = (int)$this->lid;

			$this->db->write_query("UPDATE {$table} SET packages = :language_packages WHERE lid = {$lid}", ['language_packages' => serialize_field($packages_array)]);
		}
	}

	/**
	* Updates the language in the database
	*/
	public function update()
	{
		global $venus;
		if (!parent::update()) {
			return false;
		}

		$this->update_packages();

		$venus->cache->clear_language_default();
		$venus->cache->clear_language($this->lid);
		$venus->cache->build_language($this->lid);

		return true;
	}

	protected function update_packages()
	{
		global $venus;
		if (!$this->packages_array) {
			return;
		}

		$packages_table = $this->get_packages_table();
		$strings_table = $this->get_strings_table();
		$lid = (int)$this->lid;

		$this->db->read_query("SELECT pid, name FROM {$packages_table} WHERE lid = {$lid}");
		$packages_array = $venus->db->get_list('name', 'pid');

		$dummy = new \venus\helpers\Dummy;
		foreach ($this->packages_array as $package_name => $package_title) {
			if (empty($packages_array[$package_name])) {
				continue;
			}

			$file = $this->dir . sl($this->name) . $package_name . VENUS_LANGUAGES_FILES_EXTENSION;

			$dummy->strings = [];
			$dummy->load($file);

			if (!$dummy->strings) {
				continue;
			}

			$pid = (int)$packages_array[$package_name];

			$venus->db->read_query("SELECT string, lid FROM {$strings_table} WHERE lid = {$lid} AND pid = {$pid}");
			$strings_array = $venus->db->get_list('string', 'lid');

			$i = count($strings_array);
			foreach ($dummy->strings as $string => $text) {
				if (isset($strings_array[$string])) {
					continue;
				}

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
		}
	}

	protected function insert_package($package_name, $package_title)
	{
		$insert_array =
		[
			'lid' => (int)$this->lid,
			'title' => $package_title,
			'name' => $package_name
		];

		return $this->db->insert($this->get_packages_table(), $insert_array);
	}

	protected function insert_strings($pid, $strings, $i = 0)
	{
		$lid = (int)$this->lid;
		$pid = (int)$pid;

		foreach ($strings as $string => $text) {
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
	}

	public function get_extra_blocks()
	{
		return $this->get_extra(VENUS_LANGUAGES_EXTRA_BLOCKS_DIR);
	}

	public function get_extra_widgets()
	{
		return $this->get_extra(VENUS_LANGUAGES_EXTRA_WIDGETS_DIR);
	}

	public function get_extra_dialogs()
	{
		return $this->get_extra(VENUS_LANGUAGES_EXTRA_DIALOGS_DIR);
	}

	protected function get_extra($dir)
	{
		global $venus;
		$extra = [];

		$filename = $this->dir . sl($this->name) . $dir;
		if (is_dir($filename)) {
			$venus->file->list_dir($filename, $extra, $files);
		}

		return $extra;
	}
}
