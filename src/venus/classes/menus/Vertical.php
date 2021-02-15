<?php
/**
* The Vertical With Blocks Menu Driver
* @package Venus
*/

namespace Venus\Menus;

use Venus\App;

/**
* The Vertical With Blocks Menu Driver
*/
class Vertical extends Menu implements DriverInterface
{
	/**
	* @var $class The menu's class attribute
	*/
	protected string $class = 'nav-vertical';

	/**
	* @see \Venus\Menus\DriverInterface::getHtml()
	*/
	/*public function getHtml(array $items) : string
	{
		$html = $this->getRootItems($items);
		$html.= $this->getBlocks($items);

		return $html;
	}*/

	/**
	* Returns the root items's html code
	* @param array $items The menu items
	* @return string
	*/
/*	protected function getItems(array $items) : string
	{
		$html = "<ul>\n";
		foreach ($items as $name => $item) {
			if (!empty($item->parent)) {
				continue;
			}

			$html.= $this->getRootItem($name, $item);
		}
		$html.= "</ul>\n";

		return $html;
	}
*/
	/**
	* Returns the item's html code
	* @param string $name The name of the menu item
	* @param object $item The menu item
	* @return string
	*/
	/*protected function getItem(string $name, object $item) : string
	{
		$title = App::e($item->title);
		$url = App::e($item->url ?? $this->app->uri->getEmpty());
		$image = $item->image ?? '';
		if (!$image) {
			$image = $this->app->theme->images_url . 'menu/' . $name . '.png';
		}
		$image = App::e($image);

		$html = "<li>\n";
		$html.= '<a href="' . $url . '"><img src="' . $image . '" alt="' . $title . '">' . $title . '</a>' . "\n";
		$html.= "</li>\n";

		return $html;
	}*/

	/**
	* Returns the root items's html code
	* @param array $items The menu items
	* @return string
	*/
	/*protected function getBlocks(array $items) : string
	{
		$html = "<ul>\n";
		foreach ($items as $name => $item) {
			if (!empty($item->parent)) {
				continue;
			}

			$html.= $this->getRootItem($name, $item);
		}
		$html.= "</ul>\n";

		return $html;
	}*/
}
