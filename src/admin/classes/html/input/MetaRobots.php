<?php
/**
* The Meta Robots Class
* @package Venus
*/

namespace Venus\Admin\Html\Input;

use Venus\App;

/**
* The Meta Robots Class
* Renders a field from where the user can select the meta robots option
*/
class MetaRobots extends Options
{
	protected function getOptions() : array
	{
		$options =  [
			'' => '',
			'index, follow' => App::__('meta_robots1'),
			'index, nofollow' => App::__('meta_robots2'),
			'noindex, follow' => App::__('meta_robots3'),
			'noindex, nofollow' => App::__('meta_robots4')
		];
		
		$options = $this->app->plugins->filter('admin_html_input_meta_robots_get_options', $options);
		
		return $options;
	}
}
