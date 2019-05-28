<?php
/**
* The Admin's Output Menu Class
* @package Venus
*/

namespace Venus\Admin\Output;

use Venus\Admin\App;

/**
* The Admin's Output Menu Class
* Outputs the admin menu
*/
class Menu extends \Venus\Output\Menu
{
	/**
	* @internal
	*/
	protected static $table = 'venus_admin_menu';

	/**
	* Returns the menu's table
	* @return string
	*/
	public function getTable() : string
	{
		return static::$table;
	}

	/**
	* Outputs the admin menu
	* @param string $name The name of the menu to output
	* @param bool $for_mobile If true will display the menu on mobile devices
	*/
	public function output(string $name = 'main', bool $for_mobile = true)
	{
		$menu_array = [];

		$this->app->lang->loadFile('menu');

		include($this->app->admin_dir . 'menu.php');

		//get the custom admin menus
		$menu_entries = $this->app->db->sql->select()->from($this->getTable())->orderBy('title')->get();

		if ($menu_entries) {
			//include the plugins menus
			$this->addMenus('plugin', 'plugin', 'plugins', $menu_entries, $menu_array);

			//include the custom blocks
			$this->addMenus('block', 'block', 'blocks', $menu_entries, $menu_array);

			//include the custom widgets
			$this->addMenus('widget', 'widget', 'widgets', $menu_entries, $menu_array);

			//include the global menus
			$this->addGlobalMenus($menu_entries, $menu_array);
		}

		$output = $this->getOutput($name, $menu_array, -1, $this->getAccesibleBlocks());

		echo $this->outputMenu($name, $for_mobile, $output);
	}

	/**
	* Returns the blocks the admin can access, so we can exclude from the menu the ones it can't view
	*/
	protected function getAccesibleBlocks() : ?iterable
	{
		$uid = (int)$this->app->user->uid;
		$sql = "
			SELECT name
			FROM venus_administrators_permissions AS p
			LEFT JOIN venus_admin_blocks AS b USING(bid)
			WHERE p.uid = {$uid} AND view = 1 AND name <> ''";

		$this->app->db->readQuery($sql);
		$blocks = $this->app->db->getFields();

		if (!$blocks) {
			//the admin doesn't have the permissions explicitly set. Load all block names minus the administrator blocks
			if (!$this->app->user->isSuperAdmin()) {
				$this->app->db->readQuery('SELECT name FROM venus_admin_blocks where name != :name', ['name' => VENUS_ADMIN_ADMINISTRATORS_BLOCK]);
				$blocks = $this->app->db->getFields();
			} else {
				return null;
			}
		}

		return $blocks;
	}

	/**
	* Adds menu entries to the global menu
	* @param array $menu_entries The custom menu entries to add
	* @param array $menu_array The menu array where the custom menu entries are added
	*/
	protected function addGlobalMenus(iterable $menu_entries, array &$menu_array)
	{
		foreach ($menu_entries as $menu) {
			if ($menu->type != 'global') {
				continue;
			}

			$menu_parent = '';
			if ($menu->parent) {
				$menu_parent = 'global_menu_' . $menu->parent;
			}

			if ($menu->parent && !$menu->url) {
				//add as separator
				$menu_array['global_menu_' . $menu->mid] = ['parent' => $menu_parent, 'separator' => true];
				continue;
			}

			$menu_array['global_menu_' . $menu->mid] = ['parent' => $menu_parent, 'title' => $menu->title, 'url' => $menu->url, 'block' => $menu->block];
		}
	}

	/**
	* Adds menu entries to an already existing menu. Eg: add plugin links under the Plugin menu
	* @param string $type The type to add. Only those entries from $menu_entries matching the type will be added
	* @param string $prefix The menu prefix
	* @param string $parent The parent menu
	* @param array $menu_entries The custom menu entries to add
	* @param array $menu_array The menu array where the custom menu entries are added
	*/
	protected function addMenus(string $type, string $prefix, string $parent, iterable $menu_entries, array &$menu_array)
	{
		$i = 0;
		foreach ($menu_entries as $menu) {
			if ($menu->type != $type) {
				continue;
			}

			//add a separator as the first entry
			if (!$i) {
				$menu_array[$prefix . '_menu_sep_1'] = ['parent' => $parent, 'separator' => true];
			}

			$menu_parent = $parent;
			if ($menu->parent) {
				$menu_parent = $prefix . '_menu_' . $menu->parent;
			}

			$menu_array[$prefix . '_menu_' . $menu->mid] = ['parent' => $menu_parent, 'title' => $menu->title, 'url' => $menu->url, 'block' => $menu->block];

			$i++;
		}
	}
}
