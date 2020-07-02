<?php
/**
* The Sitemap Priority Class
* @package Venus
*/

namespace Venus\Admin\Html\Input;

use Venus\App;

/**
* The Sitemap Priority Class
* Renders a field from where the user can select the sitemap priority option
*/
class SitemapPriority extends Options
{
	protected function getOptions() : array
	{
		$options = ['0' => '0', '0.1' => '0.1', '0.2' => '0.2', '0.3' => '0.3', '0.4' => '0.4', '0.5' => '0.5', '0.6' => '0.6', '0.7' => '0.7', '0.8' => '0.8', '0.9' => '0.9', '1' => '1'];
		
		$options = $this->app->plugins->filter('admin_html_input_sitemap_priority_get_options', $options);
		
		return $options;
	}
}
