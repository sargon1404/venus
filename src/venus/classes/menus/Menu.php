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
	* @var $class The menu's class attribute
	*/
	protected string $class = '';

	/**
	* @see \Venus\Menus\DriverInterface::getHtml()
	*/
	public function getHtml(array $items) : string
	{
		$html = '<nav class="' . $this->class . '">' . "\n";
		$html.= "<ul>\n";
		foreach ($items as $name => $item) {
			if (!empty($item->parent)) {
				continue;
			}

			$html.= "<li>\n";
			$html.= $this->getItem($item);
			$html.= $this->getSubitems($item, $items);
			$html.= "</li>\n";
		}
		$html.= "</ul>\n";
		$html.= '</nav>' . "\n";

		return $html;
	}

	/**
	* Returns the child menus of a menu
	* @param object $item The item to return the children for
	* @param array $items The menu items
	* @return array The child menus
	*/
	protected function getChildren(object $item, array $items) : array
	{
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
	* Returns the item's html code
	* @param object $item The menu item
	* @return string
	*/
	protected function getItem(object $item) : string
	{
		$title = App::e($item->title);
		$url = App::e($item->url ?? $this->app->uri->getEmpty());

		return '<a href="' . $url . '">' . $title . '</a>' . "\n";
	}

	/**
	* Returns the an item's subitems html code
	* @param object $item The item to return the subitems for
	* @param array $items The menu items
	* @return string
	*/
	protected function getSubitems(object $item, array $items) : string
	{
		$html = '<div id="nav-dropdown-' . App::e($item->name) . '" class="nav-dropdown">' . "\n";

		if (!empty($item->html)) {
			$html.= $item->html . "\n";
		} else {
			$children = $this->getChildren($item, $items);

			$html.= $this->getSubmenusList($children);
		}

		$html.= '</div>';

		return $html;
	}

	/**
	* Returns a submenus list
	* @param array $items The menu items
	* @return string
	*/
	protected function getSubmenusList(array $items) : string
	{
		if (!$items) {
			return '';
		}

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
