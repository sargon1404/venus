<?php
namespace Venus\Admin;

//$block_param = urlencode($venus->config->block_param);
//$controller_param = urlencode($venus->config->controller_param);

return [

	'site' 						 => ['parent' => '', 'title' => App::__('menu_site'), 'block' => 'admin-', 'url' => ''],
	'site_dashboard'		 	 => ['parent' => 'site', 'title' => App::__('menu_dashboard'), 'block' => 'dashboard', 'url' => $this->app->admin_index],
	'site_sep_1' 				 => ['parent' => 'site', 'separator' => true],
	'site_settings'			 => ['parent' => 'site', 'title' => App::__('menu_settings'), 'block' => 'config', 'url' => $this->app->uri->getAdminBlock('admin_config')],
	'site_sep_2' 				 => ['parent' => 'site', 'separator' => true],
	'site_themes'			 	 => ['parent' => 'site', 'title' => App::__('menu_themes'), 'block' => 'themes', 'url' => $this->app->uri->getAdminBlock('themes')],
	'site_languages'		 	 => ['parent' => 'site', 'title' => App::__('menu_languages'), 'block' => 'languages', 'url' => $this->app->uri->getAdminBlock('languages')],
	'site_cron'				 	 => ['parent' => 'site', 'title' => App::__('menu_cron'), 'block' => 'cron', 'url' => $this->app->uri->getAdminBlock('cron')],
	'site_sep_3' 				 => ['parent' => 'site', 'separator' => true],
	'site_log'				 	 => ['parent' => 'site', 'title' => App::__('menu_log'), 'block' => 'log', 'url' => $this->app->uri->getAdminBlock('logs')],
	'site_cache'		 		 => ['parent' => 'site', 'title' => App::__('menu_cache'), 'block' => 'cache', 'url' => $this->app->uri->getAdminBlock('cache')],
	'site_import'			    => ['parent' => 'site', 'title' => App::__('menu_import'), 'block' => 'import', 'url' => $this->app->uri->getAdminBlock('import')],


	'content' 					 => ['parent' => '', 'title' => App::__('menu_content'), 'block' => 'admin-', 'url' => '', ],
	'content_menus'			 => ['parent' => 'content', 'title' => App::__('menu_menus'), 'block' => 'menu', 'url' => $this->app->uri->getAdminBlock('menus')],
	'content_categories'		 => ['parent' => 'content', 'title' => App::__('menu_categories'), 'block' => 'categories', 'url' => $this->app->uri->getAdminBlock('categories')],
	'content_announcements'	 => ['parent' => 'content', 'title' => App::__('menu_announcements'), 'block' => 'announcements', 'url' => $this->app->uri->getAdminBlock('announcements')],
	'content_pages'			 => ['parent' => 'content', 'title' => App::__('menu_pages'), 'block' => 'pages', $this->app->uri->getAdminBlock('pages')],
	'content_comments' 		 => ['parent' => 'content', 'title' => App::__('menu_comments'), 'block' => 'comments', 'url' => $this->app->uri->getAdminBlock('comments')],
	'content_tags'				 => ['parent' => 'content', 'title' => App::__('menu_tags'), 'block' => 'tags', 'url' => $this->app->uri->getAdminBlock('tags')],
	'content_sep_1' 			 => ['parent' => 'content', 'separator' => true],
	'content_snippets'		 => ['parent' => 'content', 'title' => App::__('menu_snippets'), 'block' => 'snippets', 'url' => $this->app->uri->getAdminBlock('snippets')],
	'content_templates'		 => ['parent' => 'content', 'title' => App::__('menu_content_templates'), 'block' => 'content_templates', 'url' => $this->app->uri->getAdminBlock('content-templates')],
	'content_banners' 		 => ['parent' => 'content', 'title' => App::__('menu_banners'), 'block' => 'banners', 'url' => $this->app->uri->getAdminBlock('banners')],
	'content_sep_2' 			 => ['parent' => 'content', 'separator' => true],
	'content_404' 				 => ['parent' => 'content', 'title' => App::__('menu_404'), 'block' => '404-editor', 'url' => $this->app->uri->getAdminBlock('404-editor')],
	'content_media'		 	 => ['parent' => 'content', 'title' => App::__('menu_media'), 'block' => 'media', 'url' => $this->app->uri->getAdminBlock('media')],

	'users' 						 => ['parent' => '', 'title' => App::__('menu_users'), 'block' => 'users', 'url' => ''],
	'users_users' 				 => ['parent' => 'users', 'title' => App::__('menu_users2'), 'block' => 'users', 'url' => $this->app->uri->getAdminBlock('users')],
	'users_usergroups' 		 => ['parent' => 'users', 'title' => App::__('menu_usergroups'), 'block' => 'usergroups', 'url' => $this->app->uri->getAdminBlock('usersgroups')],
	'users_administrators' 	 => ['parent' => 'users', 'title' => App::__('menu_administrators'), 'block' => 'administrators', 'url' => $this->app->uri->getAdminBlock('administrators')],
	'users_moderators' 		 => ['parent' => 'users', 'title' => App::__('menu_moderators'), 'block' => 'moderators', 'url' => $this->app->uri->getAdminBlock('moderators')],
	'users_sep_1' 				 => ['parent' => 'users', 'separator' => true],
	'users_new_user' 			 => ['parent' => 'users', 'title' => App::__('menu_new_user'), 'block' => 'users', 'url' => $this->app->uri->getAdminBlock('users', 'add')],
	'users_merge' 				 => ['parent' => 'users', 'title' => App::__('menu_merge_users'), 'block' => 'users', 'url' => $this->app->uri->getAdminBlock('users', '', [], 'merge')],
	'users_sep_2' 				 => ['parent' => 'users', 'separator' => true],
	'users_spammers' 			 => ['parent' => 'users', 'title' => App::__('menu_spammers'), 'block' => 'users', 'url' => $this->app->uri->getAdminBlock('users', '', [], 'spammers')],
	'users_banned' 			 => ['parent' => 'users', 'title' => App::__('menu_banned'), 'block' => 'users', 'url' => $this->app->uri->getAdminBlock('users', '', [], 'banned')],


	'blocks' 					 => ['parent' => '', 'title' => App::__('menu_blocks'), 'block' => 'blocks', 'url' => ''],
	'blocks_blocks' 			 => ['parent' => 'blocks', 'title' => App::__('menu_blocks2'), 'block' => 'blocks', 'url' => $this->app->uri->getAdminBlock('blocks')],
	'blocks_admin_blocks' 	 => ['parent' => 'blocks', 'title' => App::__('menu_admin_blocks'), 'block' => 'admin-blocks', 'url' => $this->app->uri->getAdminBlock('admin-blocks')],
	'blocks_sep_1' 			 => ['parent' => 'blocks', 'separator' => true],

	'widgets' 					 => ['parent' => '', 'title' => App::__('menu_widgets'), 'block' => 'widgets', 'url' => ''],
	'widgets_widgets' 		 => ['parent' => 'widgets', 'title' => App::__('menu_widgets_widgets'), 'block' => 'widgets', 'url' => $this->app->uri->getAdminBlock('widgets')],
	'widgets_groups' 			 => ['parent' => 'widgets', 'title' => App::__('menu_widgets_groups'), 'block' => 'widgets', 'url' => $this->app->uri->getAdminBlock('widgets', '', [], 'groups')],
	'widgets_sep_1' 			 => ['parent' => 'widgets', 'separator' => true],

	'plugins' 					 => ['parent' => '', 'title' => App::__('menu_plugins'), 'block' => 'plugins', 'url' => ''],
	'plugins_plugins' 		 => ['parent' => 'plugins', 'title' => App::__('menu_plugins2'), 'block' => 'plugins', 'url' => $this->app->uri->getAdminBlock('plugins')],
	'plugins_sep_1' 			 => ['parent' => 'plugins', 'separator' => true],

	'tools' 						 => ['parent' => '', 'title' => App::__('menu_tools'), 'block' => 'admin-', 'url' => ''],
	'tools_newsletter' 		 => ['parent' => 'tools', 'title' => App::__('menu_newsletter'), 'block' => 'newsletter', 'url' => $this->app->uri->getAdminBlock('newsletter')],
	'tools_sep_1' 				 => ['parent' => 'tools', 'separator' => true],
	'tools_search'			    => ['parent' => 'tools', 'title' => App::__('menu_search'), 'block' => 'search', 'url' => $this->app->uri->getAdminBlock('search')],
	'tools_rss'					 => ['parent' => 'tools', 'title' => App::__('menu_rss'), 'block' => 'rss', 'url' => $this->app->uri->getAdminBlock('rss')],
	'tools_sitemap'			 => ['parent' => 'tools', 'title' => App::__('menu_sitemap'), 'block' => 'sitemap', 'url' => $this->app->uri->getAdminBlock('sitemap')],
	'tools_sep_2' 				 => ['parent' => 'tools', 'separator' => true],
	'tools_search_ip' 		 => ['parent' => 'tools', 'title' => App::__('menu_search_ip'), 'block' => 'search_ip', 'url' => $this->app->uri->getAdminBlock('search-ip')],
	'tools_ban_ip' 			 => ['parent' => 'tools', 'title' => App::__('menu_ban_ip'), 'block' => 'ban_ip', 'url' => $this->app->uri->getAdminBlock('ban-ip')],
	'tools_sep_3' 				 => ['parent' => 'tools', 'separator' => true],
	'tools_backup' 		 => ['parent' => 'tools', 'title' => App::__('menu_backup'), 'block' => 'backup', 'url' => $this->app->uri->getAdminBlock('backup')],

];
