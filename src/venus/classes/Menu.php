<?php
/**
* The Menu Class
* @package Venus
*/

namespace Venus;

/**
* The Menu Class
* Outputs a menu
*/
class Menu extends Item
{
	use \Mars\DriverTrait;

	/**
	* @var array $items The menu items
	*/
	protected array $items = [];

	/**
	* @var array $supported_urls Array listing the supported url types
	*/
	protected array $supported_urls = [
	];

	/**
	* @internal
	*/
	protected string $scope = 'frontend';

	/**
	* @internal
	*/
	protected static string $table = 'venus_menus';

	/**
	* @internal
	*/
	protected static string $items_table = 'venus_menu_items';

	/**
	* @var string $driver The used driver
	*/
	protected string $driver = 'horizontal';

	/**
	* @var string $driver_key The name of the key from where we'll read additional supported drivers from app->config->drivers
	*/
	protected string $driver_key = 'menus';

	/**
	* @var string $driver_interface The interface the driver must implement
	*/
	protected string $driver_interface = '\Venus\Menus\DriverInterface';

	/**
	* @var array $supported_drivers The supported drivers
	*/
	protected array $supported_drivers = [
		'horizontal' => '\Venus\Menus\Horizontal',
		'vertical' => '\Venus\Menus\Vertical'
	];

	/**
	* Builds the menu
	* @param int|string $name The menu's name or id
	* @param App $app The app object
	*/
	public function __construct(int|string $name, App $app = null)
	{
		if (!$app) {
			$app = App::get();
		}
		$this->app = $app;

		parent::__construct($name);
	}

	/**
	* Returns the items table name
	* @return string
	*/
	public function getItemsTable() : string
	{
		return static::$items_table;
	}

	/**
	* {@inheritdoc}
	*/
	public function getRow(int $id) : ?object
	{
		return $this->app->env->getMenu($id);
	}

	/**
	* {@inheritdoc}
	*/
	public function getRowByName(string $name) : ?object
	{
		return $this->app->env->getMenuByName($name);
	}

	/***
	* Loads the menu items
	*/
	protected function loadItems()
	{
		if (!$this->id || !$this->app->cache->menu_items_count) {
			return;
		}

		$this->items = $this->app->db->selectWithKey($this->getItemsTable(), 'id', '*', ['menu_id' => $this->id, 'status' => 1], 'position');
	}

	/**
	* Sorts the items, by the order field
	* @param array $items The menu items to sort
	* @return array The sorted items
	*/
	protected function sortItems(array $items) : array
	{
		uasort($items, function ($a, $b) {
			$o_a = $a->order ?? 1000000;
			$o_b = $b->order ?? 1000000;

			if ($o_a > $o_b) {
				return 1;
			} elseif ($o_a < $o_b) {
				return -1;
			} else {
				return 0;
			}
		});

		return $items;
	}

	/**
	* Generates the url of an item
	* @param object $item The item
	* @return string The url
	*/
	protected function getItemUrl(object $item) : string
	{
		if ($item->url) {
			return $item->url;
		}

		if (!$item->type || !isset($this->supported_urls[$item->type])) {
			return $this->app->uri->getEmpty();
		}

		$class = $this->supported_urls[$type];
		$obj = new $class($this->app);

		return $obj->getUrl($item);
	}

	/**
	* Outputs a menu
	* @param string $name The name of the menu to output
	*/
	public function output()
	{
		$usergroup_id = $this->app->user->usergroup_id;

		$this->icon_width = $this->app->theme->getImageWidth('menu');
		$this->icon_height = $this->app->theme->getImageHeight('menu');

		//get from the cache the menu id of this menu
		$menu_array = unserialize($this->app->cache->menu);
		if (empty($menu_array[$menu_name])) {
			return;
		}

		$menu_id = (int)$menu_array[$menu_name];
		if (!$menu_id) {
			return;
		}

		//check if we have any entries for this menu. If not, return
		$menu_data = unserialize($this->app->cache->menu_data);
		if (empty($menu_data[$menu_name])) {
			return;
		}

		//do we have the output in the cache? If so, output it
		if ($this->app->cache->menu_output) {
			$menu_output = unserialize($this->app->cache->menu_output);
			if (isset($menu_output[$menu_id][$usergroup_id])) {
				$output = $menu_output[$menu_id][$usergroup_id];
				$this->outputMenu($menu_name, $output);

				return;
			}
		}

		$menus = new \Venus\Objects\MenuEntries;
		$menus->menu_id = $menu_id;

		$menu_entries_array = $this->convertMenu($menus->load());

		$output = $this->getOutput($menu_name, $menu_entries_array);

		$menu_output = [];
		if ($this->app->cache->menu_output) {
			$menu_output = unserialize($this->app->cache->menu_output);
		}

		$menu_output[$menu_id][$usergroup_id] = $output;

		$this->app->cache->set('menu_output', $menu_output, null, true);

		$this->outputMenu($menu_name, $output);
	}

	/**
	* Determines if a menu entry can be accessed
	* @param array $menu The menu
	* @param array $blocks Array with the blocks the user has permissions to access
	* @return bool
	*/
	/*protected function canAccess(array $menu, ?iterable $blocks = null) : bool
	{
		if ($blocks === null) {
			return true;
		}

		$block = $menu['block'] ?? false;

		if ($block) {
			if (!in_array($block, $blocks)) {
				return false;
			}
		}

		return true;
	}*/

	/**
	* Generates the html code necesarilly to display a menu
	* @return string The html code of the generated menu
	*/
	public function getOutput() : string
	{
		if (!$this->items) {
			return '';
		}

		return $this->handle->getHtml($this->items);

		///vertical menu
		/*$for_mobile = 'false';
		if ($this->app->device->isMobile()) {
			$for_mobile = 'true';
		}

		$name = App::e($this->name);

		$html = '<a href="javascript:void(0)" class="toggle-menu" id="toggle-menu-' . $name . '" data-target="menu-' . $name . '"><span></span><span></span><span></span></a>';
		$html.= '<ul id="menu-' . $name . '">' . "\n";

		foreach ($this->items as $menu_id => $menu) {
			$parent = $menu->parent ?? false;
			if ($parent) {
				continue;
			}

			$is_separator = $menu->separator ?? false;
			if ($is_separator) {
				$html.= '<li class="separator"></li>' . "\n";
				continue;
			}

			$html.= "<li>\n";
			$html.= $this->getMenu($menu);
			$html.= $this->getSubmenu($menu_id);
			$html.= "</li>\n";
		}

		$html.= "</ul>\n";
		$html.= "<script>venus.menu.build('menu-{$name}', {$for_mobile})</script>";

		return $html;*/
	}

	/**
	* Builds the submenu of a menu
	* @param string $menu_parent The parent of the submenu
	* @param string $prefix The prefix to add to the menu, if any
	* @return string The submenu
	*/
	public function getSubmenu(string $menu_parent, string $prefix = "\t") : string
	{
		$i = 0;
		$count = 0;
		$separator = '';

		$html = $prefix . "<ul>\n";
		foreach ($this->items as $menu_id => $menu) {
			$parent = $menu->parent ?? false;
			if ($parent != $menu_parent) {
				continue;
			}

			$i++;

			$is_separator = $menu->separator ?? false;
			if ($is_separator) {
				$separator.= $prefix . '<li class="separator"></li>' . "\n";
				continue;
			} else {
				$count++;
			}

			if ($separator) {
				$html.= $separator;
				$separator = '';
			}

			$html.= $prefix . "<li>\n";
			$html.= $this->getMenu($menu);
			$html.= $this->getSubmenu($menu_id, $prefix . "\t");
			$html.= $prefix . "</li>\n";
		}

		$html.= $prefix . "</ul>\n";

		if (!$i || !$count) {
			return '';
		}

		return $html;
	}

	/**
	* Returns the generated html code of a menu
	* @param object $menu The menu
	* @param string $prefix The prefix, if any
	* @return string The menu's html
	*/
	protected function getMenu(object $menu, $prefix = '') : string
	{
		$title = $menu->title ?? '';
		$url = $menu->url ?? '';
		$icon = $menu->icon ?? '';
		$icon_alt =  $menu->seo_image_alt ?? '';
		$seo_title = $menu->seo_title ?? '';
		$seo_target = $menu->seo_target ?? '';
		$seo_rel = $menu->seo_rel ?? '';

		if ($seo_target == -1) {
			$seo_target = $this->app->config->menu_seo_target;
		}
		if ($seo_rel == -1) {
			$seo_rel = $this->app->config->menu_seo_rel;
		}
		if ($icon) {
			$icon = $this->app->html->img($icon, $this->icon_width, $this->icon_height, $icon_alt);
		}

		$attributes = [
			'class' => 'menu-item',
			'title' => $seo_title,
			'target' => $seo_target,
			'rel' => $seo_rel
		];

		return "\t" . $prefix . $this->app->html->a($url, $icon . App::e($title), $attributes, false);
	}
}
