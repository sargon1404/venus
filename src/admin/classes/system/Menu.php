<?php
/**
* The System's Admin Menu Class
* @package Venus
*/

namespace Venus\Admin\System;

use Venus\Admin\App;

/**
* The Admin Language Class
* The system's admin language extension
*/
class Menu extends \Venus\Menu
{
	/**
	* {@inheritdoc}
	*/
	protected string $name = 'admin';

	/**
	* @var array $blocks The blocks the user has access to
	*/
	protected array $blocks = [];

	/**
	* @var array $blocks_denied The blocks the user doesn't have access to
	*/
	protected array $blocks_denied = [];

	/**
	* @var array $supported_urls Array listing the supported url types
	*/
	protected array $supported_urls = [
		'block' => '\Venus\Admin\Menu\Block',
		'plugin' => '\Venus\Admin\Menu\Plugin',
		'widget' => '\Venus\Admin\Menu\Widget'
	];

	/**
	* @var string $driver The used driver
	*/
	protected string $driver = 'admin';

	/**
	* Builds the admin's menu
	* @param App $app The app object
	*/
	public function __construct(App $app = null)
	{
		parent::__construct('', $app);

		$this->addSupportedDriver('admin', '\Venus\Admin\Menus\Admin');

		$this->readItems();
	}

	/**
	* Adds a menu
	* @param string $name The name of the menu
	* @param string $title The menu's title
	* @param string $url The menu's url
	* @param string $block The block the menu is pointing to, if any
	* @param int $order The menu's order
	* @param string $html The dropdown's html code, if any
	* @return $this
	*/
	public function addMenu(string $name, string $title, string $url = '', string $block = '', int $order = 5000, string $html = '')
	{
		$this->items[$name] = ['title' => $title, 'url' => $url, 'block' => $block, 'order' => $order, 'html' => $html];

		return $this;
	}

	/**
	* Adds a section to a menu's dropdown
	* @param string $name The name of the section
	* @param string $parent The menu to add the section to
	* @param string $title The section's title
	* @param int $order The section's order
	* @param string $html The section's html code, if any
	* @return $this
	*/
	public function addSection(string $name, string $parent, string $title, int $order = 5000, string $html = '')
	{
		if (!isset($this->items[$parent])) {
			return $this;
		}

		if (!isset($this->items[$parent]['items'])) {
			$this->items[$parent]['items'] = [];
		}

		$this->items[$parent]['items'][$name] = ['title' => $title, 'order' => $order, 'html' => $html, 'items' => []];

		return $this;
	}

	/**
	* Adds a submenu
	* @param string $name The name of the menu
	* @param string $parent The name of the parent menu
	* @param string $title The menu's title
	* @param string $url The menu's url
	* @param string $section The section where the menu will be placed. If empty, it will be placed in the first section
	* @param string $block The block the menu is pointing to, if any
	* @param int $order The menu's order
	* @param string $html Html code, if any
	* @return $this
	*/
	public function addSubmenu(string $name, string $parent, string $title, string $url, string $section = '', string $block = '', int $order = 5000, string $html = '')
	{
		if (!isset($this->items[$parent])) {
			return $this;
		}

		if ($section) {
			if (!isset($this->items[$parent]['items'][$section])) {
				return $this;
			}
		} else {
			$section = array_key_first($this->items[$parent]['items']);
		}

		$this->items[$parent]['items'][$section]['items'][$name] = ['title' => $title, 'url' => $url, 'block' => $block, 'order' => $order, 'html' => $html];

		return $this;
	}

	/**
	* Outputs the admin menu
	* @param string $name The name of the menu to output
	*/
	public function output()
	{
		$this->handle = $this->getHandle();

		$this->loadBlocks();
		$this->filterItems();

		echo $this->getOutput();
	}

	/**
	* Reads the menu items from the cache
	*/
	protected function readItems()
	{
		$lang_name = $this->app->lang->name;

		$menus = $this->app->cache->get('menus');
		if (isset($menus[$lang_name])) {
			$this->items = $menus[$this->app->lang->name];
		} else {
			//generate the menu items for this language
			$this->loadItems();

			$menus[$lang_name] = $this->items;

			$this->app->cache->set('menus', $menus);
		}
	}

	/**
	* Loads the admin menu items
	*/
	public function loadItems()
	{
		$this->app->lang->loadFile('menu');

		$this->loadFromFile();
		$this->loadCustomItems();

		$this->convertItems();
		$this->sortAllItems();

		$this->items = $this->app->plugins->filter('admin_system_menu_get_items', $this->items);

		return $this->items;
	}

	/**
	* Loads menu items from the menu.php file
	*/
	protected function loadFromFile()
	{
		$this->items = include($this->app->admin_dir . 'menu.php');
	}

	/**
	* Loads the custom menu items
	*/
	protected function loadCustomItems()
	{
		/*$this->addMenu('some-link', 'Some Link', 'https://www.google.com', '', 650);
		$this->addSection('some-link-section1', 'some-link', 'Custom Section 1');
		$this->addSection('some-link-section2', 'dashboard', 'Custom Section 1');
		$this->addSection('some-link-section3', 'site', 'Custom Section 1', 10);

		$this->addSubmenu('some-submenu', 'site', 'Custom Section 1', 'https://www.google.com', 'some-link-section3');*/
	}

	/**
	* Converts the items from arrays to objects, for easier manipulation
	*/
	protected function convertItems()
	{
		foreach ($this->items as $name => &$item) {
			$item['name'] = $name;

			$item = App::toObject($item);

			if (isset($item->items)) {
				foreach ($item->items as &$section) {
					if (isset($section['items'])) {
						foreach ($section['items'] as &$menu) {
							foreach ($menu['menus'] as &$menu_items) {
								$menu_items = App::toObject($menu_items);
							}

							$menu = App::toObject($menu);
						}
					}

					$section = App::toObject($section);
				}
			}
		}
	}

	/**
	* Sorts the menu items, sections and subitems
	* @param array $items The menu items to sort
	* @return array $items The sorted menu items
	*/
	protected function sortAllItems()
	{
		//sort the menus
		$this->items = $this->sortItems($this->items);

		//sort the sections & subitems of each section
		foreach ($this->items as &$item) {
			if (isset($item->items)) {
				$item->items = $this->sortitems($item->items);

				foreach ($item->items as &$section) {
					if (isset($section->items)) {
						foreach ($section->items as &$menu) {
							$menu->menus = $this->sortItems($menu->menus);
						}

						$section->items = $this->sortItems($section->items);
					}
				}
			}
		}
	}

	/**
	* Loads the blocks the admin can access, so we can exclude from the menu the ones it can't view
	*/
	protected function loadBlocks()
	{
		if ($this->app->user->isSuperAdmin()) {
			//super admins have access to all blocks
			return;
		}
		$user_id = $this->app->user->id;

		$sql = "
			SELECT name
			FROM venus_administrators_permissions AS p
			LEFT JOIN venus_admin_blocks AS b ON b.id = p.block_id
			WHERE p.user_id = {$user_id} AND view = 1 AND name <> ''";

		$this->app->db->readQuery($sql);
		$this->blocks = $this->app->db->getFields();

		if (!$this->blocks) {
			//the admin doesn't have the permissions explicitly set. Load all block names minus the administrator blocks
			$this->blocks_denied[] = App::ADMINISTRATORS_BLOCK;
		}
	}

	/**
	* Filters the items the user doesn't have access to
	*/
	protected function filterItems()
	{
		foreach ($this->items as $key => $item) {
			if (!$this->canAccess($item)) {
				unset($this->items[$key]);
			}
		}
	}

	/**
	* @see \Venus\Menu::canAccess()
	* {inheritdoc}
	*/
	protected function canAccess(object $item) : bool
	{
		if (!isset($item->block)) {
			return true;
		}
		if (in_array($item->block, $this->blocks_denied)) {
			return false;
		}
		if ($this->blocks) {
			if (!in_array($item->block, $this->blocks)) {
				return false;
			}
		}

		return true;
	}
}
