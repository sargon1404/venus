<?php
namespace Venus\Admin;

return [

	'dashboard' => ['title' => App::__('menu_dashboard'), 'order' => 0, 'block' => 'dashboard', 'url' => $this->app->admin_index],

	'site' => ['title' => App::__('menu_site'), 'order' => 100],
		'site_settings' => ['parent' => 'site', 'title' => App::__('menu_settings'), 'order' => 100, 'block' => 'config', 'url' => $this->app->uri->getAdminBlock('admin_config')],
		'site_themes' => ['parent' => 'site', 'title' => App::__('menu_themes'), 'order' => 200, 'block' => 'themes', 'url' => $this->app->uri->getAdminBlock('themes')],
		'site_languages' => ['parent' => 'site', 'title' => App::__('menu_languages'), 'order' => 300, 'block' => 'languages', 'url' => $this->app->uri->getAdminBlock('languages')],
		'site_cron' => ['parent' => 'site', 'title' => App::__('menu_cron'), 'order' => 400, 'block' => 'cron', 'url' => $this->app->uri->getAdminBlock('cron')],
		'site_log' => ['parent' => 'site', 'title' => App::__('menu_log'), 'order' => 500, 'block' => 'log', 'url' => $this->app->uri->getAdminBlock('logs')],
		'site_cache' => ['parent' => 'site', 'title' => App::__('menu_cache'), 'order' => 600, 'block' => 'cache', 'url' => $this->app->uri->getAdminBlock('cache')],

	'content'	=> ['title' => App::__('menu_content'), 'order' => 200],


	'users' => ['title' => App::__('menu_users'), 'order' => 300],


	'blocks' => ['title' => App::__('menu_blocks'), 'order' => 400],


	'widgets' => ['title' => App::__('menu_widgets'), 'order' => 500],


	'plugins' => ['title' => App::__('menu_plugins'), 'order' => 600],


	'tools' => ['title' => App::__('menu_tools'), 'order' => 700],
];
