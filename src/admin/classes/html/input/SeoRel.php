<?php
/**
* The Seo Rel Class
* @package Venus
*/

namespace Venus\Admin\Html\Input;

use Venus\App;

/**
* The Seo Rel Class
* Renders a field from where the user can select the seo rel option
*/
class SeoRel extends Options
{
	protected function getOptions() : array
	{
		$options =  [
			'' => App::__('seo_rel_follow'),
			'nofollow' => App::__('seo_rel_nofollow')
		];
		
		$options = $this->app->plugins->filter('admin_html_input_seo_rel_get_options', $options);
		
		return $options;
	}
}
