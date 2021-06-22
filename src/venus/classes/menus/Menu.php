<?php
/**
* The Menu Base Class
* @package Venus
*/

namespace Venus\Menus;

use Venus\App;

/**
* The Menu Base Class
*/
abstract class Menu
{
	use \Venus\AppTrait;

	/**
	* @var $class The menu's id attribute
	*/
	protected string $id = '';

	/**
	* @var $class The menu's class attribute
	*/
	protected string $class = '';

	/**
	* @see \Venus\Menus\DriverInterface::getHtml()
	*/
	public function getHtml(string $name, array $items) : string
	{
		$name = App::e($name);

		$html = '<div class="nav-wrapper">';
		$html.= '<nav id="menu-' . $name . '" class="' . $this->class . '">' . "\n";
		$html.= '<a href="javascript:void(0)" class="toggle-menu" id="toggle-menu-' . $name . '" data-target="menu-items-' . $name . '"><span></span><span></span><span></span></a>';
		$html.= '<ul id="menu-items-' . $name . '">' . "\n";

		foreach ($items as $item_name => $item) {
			$dropdown = $this->getDropdown($item);

			$class = '';
			if ($dropdown) {
				$class = ' class="has-dropdown"';
			}

			$html.= "<li{$class}>\n";
			$html.= $this->getItem($item);
			$html.= $dropdown;
			$html.= "</li>\n";
		}

		$html.= "</ul>\n";
		$html.= '</nav>' . "\n";
		$html.= "<script>venus.menu.build('menu-items-{$name}')</script>";
		$html.= '</div>' . "\n";

		return $html;
	}

	/**
	* Returns the item's html code
	* @param object $item The menu item
	* @return string
	*/
	protected function getItem(object $item) : string
	{
		return '<a href="' . App::e($item->url ?? $this->app->uri->getEmpty()) . '">' . App::e($item->title) . '</a>' . "\n";
	}

	/**
	* Returns the an item's drowdown code
	* @param object $item The item to return the dropdown for
	* @param array $items The menu items
	* @return string
	*/
	protected function getDropdown(object $item) : string
	{
		if (empty($item->html) && empty($item->items)) {
			return '';
		}

		$html = '<div id="nav-dropdown-' . App::e($item->name) . '" class="nav-dropdown">' . "\n";

		if (!empty($item->html)) {
			$html.= $item->html . "\n";
		} else {
			$html.= $this->getDropdownHtml($item) . "\n";
		}

		$html.= '</div>';

		return $html;
	}

	/**
	* Returns the html code of a dropdown
	* @param array $items The menu items
	* @return string
	*/
	protected function getDropdownHtml(object $item) : string
	{
		if (!$item->items) {
			return '';
		}

		$items = $this->sortItems($item->items);

		$html= "<ul>\n";

		foreach ($items as $item) {
			$html.= "<li>\n";
			$html.= $this->getSubitem($item);
			$html.= "</li>\n";
		}

		$html.= "</ul>\n";

		return $html;
	}

	/**
	* Returns the child menus of a menu
	* @param object $item The item to return the children for
	* @param array $items The menu items
	* @return array The child menus
	*/
	protected function getSubitems(object $item, array $items) : array
	{
		var_dump($items);
		die;
		$subitems = [];
		foreach ($items as $name => $subitem) {
			if (empty($subitem->parent)) {
				continue;
			}

			if ($item->name == $subitem->parent) {
				$subitems[$name] = $subitem;
			}
		}

		return $subitems;
	}

	/**
	* Returns the subitem's html code
	* @param object $item The menu item
	* @return string
	*/
	protected function getSubitem(object $item) : string
	{
		$title = App::e($item->title);
		$url = App::e($item->url ?? $this->app->uri->getEmpty());

		return '<a href="' . $url . '">' . $title . '</a>' . "\n";
	}
}
