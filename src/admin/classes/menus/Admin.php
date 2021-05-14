<?php
/**
* The Vertical With Blocks Menu Driver
* @package Venus
*/

namespace Venus\Admin\Menus;

use Venus\App;

/**
* The Vertical With Blocks Menu Driver
*/
class Admin extends \Venus\Menus\Vertical
{

	/**
	* @see \Venus\Menus\Menu::getItems()
	* {@inheritdoc}
	*/
	protected function getItem(object $item) : string
	{
		$image = $item->image ?? '';
		if ($image) {
			$image = $this->app->url_static . $image;
		} else {
			$image = $this->app->theme->images_url . 'menu/' . $item->name . '.png';
		}
		//var_dump($item);die;
		return '<a href="' . App::e($item->url ?? $this->app->uri->getEmpty()) . '"><img src="' . App::e($image) . '" alt="' . App::e($item->title) . '">' . App::e($item->title) . '</a>' . "\n";
	}

	/**
	* @see \Venus\Menus\Menu::getDropdownHtml()
	* {@inheritdoc}
	*/
	protected function getDropdownHtml(array $sections) : string
	{
		if (!$sections) {
			return '';
		}

		$html = '';
		foreach ($sections as $name => $section) {
			$html.= '<section id="nav-dropdown-section-' . App::e($name) . '">' . "\n";
			if (!empty($section->title)) {
				$html.= '<h3>' . App::e($section->title) . '</h3>' . "\n";
			}

			//$html.= $this->getDropdownSectionHtml($section);

			$html.= "</section>\n";
		}

		return $html;
	}

	/**
	* Returns the html code of a dropdown section
	* @param array $section The section
	* @return string The html code
	*/
	protected function getDropdownSectionHtml(array $section) : string
	{
		if (!empty($section->html)) {
			return $section->html;
		}
		var_dump($items);
		die;

		$html = "<ul>\n";
		foreach ($items as $items) {
		}
		$html.= "</ul>\n";

		return $html;
	}

	/**
	* @see \Venus\Menus\Menu::getSubitems()
	* {@inheritdoc}
	*/
	/*protected function getSubitems(object $item, array $items) : string
	{
		$url = $item->url ?? '';
		if ($url) {
			return '';
		}

		return parent::getSubitems($item, $items);
	}*/

	/**
	* Returns the item's html code
	* @param string $name The name of the menu item
	* @param object $item The menu item
	* @return string
	*/
	/*protected function getRootItem(string $name, object $item) : string
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
		return '';
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
