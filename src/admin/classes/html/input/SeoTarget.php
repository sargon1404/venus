<?php
/**
* The Seo Target Class
* @package Venus
*/

namespace Venus\Admin\Html\Input;

use Venus\App;

/**
* The Seo Target Class
* Renders a field from where the user can select the seo target option
*/
class SeoTarget extends Options
{
	protected function getOptions() : array
	{
		$options =  [
			'' => App::__('seo_target_same'),
			'_blank' => App::__('seo_target_new')
		];
		
		$options = $this->app->plugins->filter('admin_html_input_seo_target_get_options', $options);
		
		return $options;
	}
}
