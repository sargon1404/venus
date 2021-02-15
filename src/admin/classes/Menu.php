<?php
/**
* The Admin Menu Class
* @package Venus
*/

namespace Venus\Admin;

/**
* The Admin Menu Class
* Outputs the admin menu
*/
class Menu extends \Venus\Menu
{

	/**
	* @internal
	*/
	protected string $scope = 'admin';

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
	* Builds the menu
	* @param App $app The app object
	*/
	public function __construct(App $app = null)
	{
		parent::__construct('', $app);

		$this->addSupportedDriver('admin', '\Venus\Admin\Menus\Admin');
	}

	/**
	* Outputs the admin menu
	* @param string $name The name of the menu to output
	*/
	public function output()
	{
		$this->app->lang->loadFile('menu');

		$this->handle = $this->getHandle();

		$this->loadBlocks();
		//$this->loadItems();
		$this->loadFromFile($this->app->admin_dir . 'menu.php');
		$this->filterItems();

		echo $this->getOutput();
	}

	/***
	* Loads the menu items
	*/
	protected function loadItems()
	{
		if (!$this->id || !$this->app->cache->menu_items_count) {
			return;
		}

		//set the parent for plugins/blocks/widgets to the appropiate areas
		$types = [
			'plugin' => 'plugins',
			'block' => 'blocks',
			'widget' => 'widgets'
		];

		$this->items = $this->app->db->selectWithKey($this->getItemsTable(), 'id', '*', ['menu_id' => $this->id, 'status' => 1], 'title');

		foreach ($this->items as &$item) {
			$item->title = App::__($item->title);

			if (!$item->type || !isset($types[$item->type])) {
				continue;
			}

			if (!$item->parent) {
				$item->parent = $types[$item->type];
			}

			$item->block = $types[$item->type];
			$item->url = $this->getItemUrl($item);
		}
	}

	/**
	* Loads menu items from a file
	* @param string $filename The filename to load the menus from
	*/
	protected function loadFromFile(string $filename)
	{
		$items = [];
		$menus = include($filename);
		foreach ($menus as $name => $menu) {
			$menu['name'] = $name;

			$items[$name] = (object) $menu;
		}

		$this->items = $items + $this->items;
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
			if (!isset($item->block)) {
				continue;
			}

			if (in_array($item->block, $this->blocks_denied)) {
				unset($this->items[$key]);
			}

			if ($this->blocks) {
				if (!in_array($item->block, $this->blocks)) {
					unset($this->items[$key]);
				}
			}
		}
	}
}
