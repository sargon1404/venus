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

		return '<a href="' . App::e($item->url ?? $this->app->uri->getEmpty()) . '"><img src="' . App::e($image) . '" alt="' . App::e($item->title) . '">' . App::e($item->title) . '</a>' . "\n";
	}

	/**
	* @see \Venus\Menus\Menu::getDropdownHtml()
	* {@inheritdoc}
	*/
	protected function getDropdownHtml(object $item) : string
	{
		$html = '<a href="javascript:void(0)" class="nav-dropdown-close"></a>' . "\n";
		$html.= '<h2>' . App::e($item->title) . '</h2>' . "\n";
		$html.= '<div class="nav-dropdown-sections">' . "\n";

		foreach ($item->items as $name => $section) {
			$html.= '<section id="nav-dropdown-section-' . App::e($name) . '">' . "\n";
			if (!empty($section->title)) {
				$html.= '<h3>' . App::e($section->title) . '</h3>' . "\n";
			}

			$html.= $this->getDropdownSectionHtml($section);

			$html.= "</section>\n";
		}

		$html.= '</div>' . "\n";

		return $html;
	}

	/**
	* Returns the html code of a dropdown section
	* @param object $section The section
	* @return string The html code
	*/
	protected function getDropdownSectionHtml(object $section) : string
	{
		if (!empty($section->html)) {
			return $section->html;
		}

		$html = '';

		foreach ($section->items as $menu_name => $menu) {
			$title =  $menu->title ?? '';
			if ($title) {
				$html.= '<h3>' . App::e($title) . '</h3>' . "\n";
			}

			$html.= "<ul>\n";
			foreach ($menu->menus as $item) {
				$html.= '<li><a href="' . App::e($item->url) . '">' . App::e($item->title) . '</a></li>' . "\n";
			}
			$html.= "</ul>\n";
		}

		return $html;
	}
}
