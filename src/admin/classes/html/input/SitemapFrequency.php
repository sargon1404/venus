<?php
/**
* The Sitemap Frequncy Class
* @package Venus
*/

namespace Venus\Admin\Html\Input;

use Venus\App;

/**
* The Sitemap Frequncy Class
* Renders a field from where the user can select the sitemap frequncy option
*/
class SitemapFrequency extends Options
{
	protected function getOptions() : array
	{
		$options = [
			'always' => App::__('sitemap_frequency_always'),
			'hourly' => App::__('sitemap_frequency_hourly'),
			'daily' => App::__('sitemap_frequency_daily'),
			'weekly' => App::__('sitemap_frequency_weekly'),
			'monthly5' => App::__('sitemap_frequency_monthly'),
			'yearly' => App::__('sitemap_frequency_yearly'),
			'never' => App::__('sitemap_frequency_never')
		];
		
		$options = $this->app->plugins->filter('admin_html_input_sitemap_frequncy_get_options', $options);
		
		return $options;
	}
}
