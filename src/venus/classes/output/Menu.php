<?php
/**
* The Output Menu Class
* @package Venus
*/

namespace Venus\Output;

use Venus\App;

/**
* The Output Menu Class
* Outputs a menu
*/
class Menu
{
	/**
	* @var int $icon_width The icon's width
	*/
	public int $icon_width = 0;

	/**
	* @var int $icon_height The icon's height
	*/
	public int $icon_height = 0;

	/**
	* Outputs a menu
	* @param string $name The name of the menu to output
	* @param bool $for_mobile If true will display the menu on mobile devices
	*/
	public function output(string $menu_name = 'main', bool $for_mobile = true)
	{
		$ugid = $this->app->user->ugid;

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
			if (isset($menu_output[$menu_id][$ugid])) {
				$output = $menu_output[$menu_id][$ugid];
				$this->outputMenu($menu_name, $for_mobile, $output);

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

		$menu_output[$menu_id][$ugid] = $output;

		$this->app->cache->update('menu_output', serialize($menu_output));

		$this->outputMenu($menu_name, $for_mobile, $output);
	}

	protected function convertMenu($menu_array)
	{
		if (is_array($menu_array)) {
			return $menu_array;
		}

		$menu_array2 = [];
		foreach ($menu_array as $menu) {
			$mid = $menu->mid;
			$menu_array2[$mid] = $menu;
		}

		return $menu_array2;
	}

	/**
	* Outputs the menu
	* @param string $name The name of the menu to output
	* @param bool $for_mobile If true will display the menu on mobile devices
	* @param string $output The generated menu output
	*/
	protected function outputMenu(string $name, bool $for_mobile, string $output)
	{
		$show_mobile_menu = false;
		if ($for_mobile && $this->app->device->isMobile()) {
			$show_mobile_menu = true;
		}

		if ($show_mobile_menu) {
			echo '<div class="menu-mobile"><a href="javascript:void(0)" onclick="venus.menu.toggle(\'menu-' . App::ejsc($name) . '\')"><img src="' . App::e($this->app->theme->images_url) . 'menu-mobile.png" alt="Menu" />' . App::estr('menu_mobile') . '</a></div>' . "\n";
			echo '<div class="menu-mobile-container" id="menu-' . e($name) . '-container">' . "\n";
			echo $output . "\n";
			echo '</div>' . "\n";

			if ($this->app->device->isMobile()) {
				//output for mobile devices the javascript code
				echo '<script type="text/javascript">' . "\n";
				echo 'venus.menu.build(\'menu-' . App::ejsc($name) . '\');' . "\n";
				echo '</script>' . "\n";
			}
		} else {
			echo $output;
		}
	}

	/**
	* Determines if a menu entry can be accessed
	* @param array $menu The menu
	* @param array $blocks Array with the blocks the user has permissions to access
	* @return bool
	*/
	protected function canAccess(array $menu, ?iterable $blocks = null) :bool
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
	}

	/**
	* Generates the html code necesarilly to display a menu
	* @param string $name The menu's name
	* @param iterable $menu_array All the elements of the menu.
	* @param int $selected The currently selected menu title,if any
	* @param array $blocks Array with the blocks the user has permissions to access
	* @return string The html code of the generated menu
	*/
	public function getOutput(string $name, iterable $menu_array, int $selected = -1, ?iterable $blocks = null) : string
	{
		if (!$menu_array) {
			return '';
		}

		$html = '<ul id="menu-' . e($name) . '">' . "\n";

		foreach ($menu_array as $name => $menu) {
			$parent = $menu['parent'] ?? false;
			$is_separator = $menu['separator'] ?? false;

			if ($parent) {
				continue;
			}
			if (!$this->canAccess($menu, $blocks)) {
				continue;
			}

			if ($is_separator) {
				$html.= '<li class="separator"></li>' . "\n";
				continue;
			}

			$class = '';
			if ($name == $selected) {
				$class = ' class="selected"';
			}

			$submenus = $this->buildSubmenu($menu_array, $name, $blocks);
			if (!$submenus) {
				$url = $menu['url'] ?? '';
				if (!$url) {
					continue;
				}
			}

			$html.= "<li{$class}>\n";
			$html.= $this->getMenu($name, $menu);
			$html.= $submenus;
			$html.= "</li>\n";
		}

		$html.= "</ul>\n";

		return $html;
	}

	/**
	* Builds the submenus of a menu
	* @param iterable $menu_array All the elements of the menu
	* @param string $menu_parent The parent of the submenu
	* @param array $blocks Array with the blocks the user has permissions to access
	* @param string $prefix The prefix to add to the menu, if any
	* @return string The submenus
	*/
	public function buildSubmenu(iterable $menu_array, string $menu_parent, ?iterable $blocks = null, string $prefix = "\t") : string
	{
		$i = 0;
		$vi = 0;
		$separator = '';

		$html = $prefix . "<ul>\n";
		foreach ($menu_array as $name => $menu) {
			$parent = $menu['parent'] ?? false;
			$is_separator = $menu['separator'] ?? false;

			if ($parent != $menu_parent) {
				continue;
			}
			if (!$this->canAccess($menu, $blocks)) {
				continue;
			}

			$i++;

			if ($is_separator) {
				$separator.= $prefix . '<li class="separator"></li>' . "\n";
				continue;
			} else {
				$vi++;
			}

			if ($separator) {
				$html.= $separator;
				$separator = '';
			}

			$html.= $prefix . "<li>\n";
			$html.= $this->getMenu($name, $menu, $prefix);
			$html.= $this->buildSubmenu($menu_array, $name, $blocks, $prefix . "\t");
			$html.= $prefix . "</li>\n";
		}

		$html.= $prefix . "</ul>\n";

		if (!$i || !$vi) {
			return '';
		}

		return $html;
	}

	/**
	* Returns the generated html code of a menu
	* @param string $name The menu's name
	* @param array $menu The menu
	* @param string $prefix The prefix, if any
	* @return string The menu's html
	*/
	protected function getMenu(string $name, array $menu, $prefix = '') : string
	{
		$title = $menu['title'] ?? '';
		$url = $menu['url'] ?? '';
		$icon = $menu['icon'] ?? '';
		$icon_alt =  $menu['seo_image_alt'] ?? '';
		$seo_title = $menu['seo_title'] ?? '';
		$seo_target = $menu['seo_target'] ?? '';
		$seo_rel = $menu['seo_rel'] ?? '';

		if (!$url) {
			$url = $this->app->uri->getEmpty();
		}
		if ($seo_target == -1) {
			$seo_target = $this->app->config->menu_seo_target;
		}
		if ($seo_rel == -1) {
			$seo_rel = $this->app->config->menu_seo_rel;
		}
		if ($icon) {
			$icon = $this->app->html->img($icon, $this->icon_width, $this->icon_height, $icon_alt);
		}

		$attributes = $this->app->html->buildAttributes(['class' => 'menu-' . $name ,'title' => $seo_title, 'target' => $seo_target, 'rel' => $seo_rel]);

		return "\t" . $prefix . '<a href="' . App::e($url) . '"' . $attributes . '>' . $icon . App::e($title) . '</a>' . "\n";
	}
}
