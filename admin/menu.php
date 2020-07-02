<?php
namespace Venus\Admin;

//$block_param = urlencode($venus->config->block_param);
//$controller_param = urlencode($venus->config->controller_param);

return [
	'system' 					 => ['parent' => '', 'title' => App::__('menu_system')],
	'system_dashboard'		 => ['parent' => 'system', 'title' => App::__('menu_dashboard'), 'block' => 'dashboard', 'url' => $this->app->admin_index],
	'system_sep_1' 			 => ['parent' => 'system', 'separator' => true],
	'system_log'				 => ['parent' => 'system', 'title' => App::__('menu_log'), 'block' => 'admin-log', 'url' => $this->app->uri->getAdminBlock('admin_logs')],
	'system_log_log'			 => ['parent' => 'system_log', 'title' => App::__('menu_log1'), 'block' => 'admin-log', 'url' => $this->app->uri->getAdminBlock('admin_logs')],
	'system_log_errors'		 => ['parent' => 'system_log', 'title' => App::__('menu_log2'), 'block' => 'admin-log', 'url' => $this->app->uri->getAdminBlock('admin_logs', 'errors')],
	'system_log_login'		 => ['parent' => 'system_log', 'title' => App::__('menu_log3'), 'block' => 'admin-log', 'url' => $this->app->uri->getAdminBlock('admin_logs', 'admin')],
	'system_cache'		 		 => ['parent' => 'system', 'title' => App::__('menu_cache'), 'block' => 'admin-clear_cache', 'url' => $this->app->uri->getAdminBlock('admin_clear_cache')],
	'system_import'			 => ['parent' => 'system', 'title' => App::__('menu_import'), 'block' => 'admin-import', 'url' => $this->app->uri->getAdminBlock('admin_import')],
	'system_sep_2' 			 => ['parent' => 'system', 'separator' => true],
	'system_search'			 => ['parent' => 'system', 'title' => App::__('menu_search'), 'block' => 'admin-search', 'url' => $this->app->uri->getAdminBlock('admin_search')],
	'system_rss'				 => ['parent' => 'system', 'title' => App::__('menu_rss'), 'block' => 'admin-rss', 'url' => $this->app->uri->getAdminBlock('admin_rss')],
	'system_sitemap'			 => ['parent' => 'system', 'title' => App::__('menu_sitemap'), 'block' => 'admin-sitemap', 'url' => $this->app->uri->getAdminBlock('admin_sitemap')],


	'site' 						 => ['parent' => '', 'title' => App::__('menu_site'), 'block' => 'admin-', 'url' => ''],
	'site_settings'			 => ['parent' => 'site', 'title' => App::__('menu_settings'), 'block' => 'admin-settings', 'url' => $this->app->uri->getAdminBlock('admin_config')],
	'site_sep_1' 				 => ['parent' => 'site', 'separator' => true],
	'site_themes'			 	 => ['parent' => 'site', 'title' => App::__('menu_themes'), 'block' => 'themes', 'url' => $this->app->uri->getAdminBlock('themes')],
	'site_languages'		 	 => ['parent' => 'site', 'title' => App::__('menu_languages'), 'block' => 'languages', 'url' => $this->app->uri->getAdminBlock('languages')],
	'site_cron'				 	 => ['parent' => 'site', 'title' => App::__('menu_cron'), 'block' => 'cron', 'url' => $this->app->uri->getAdminBlock('cron')],
	'site_sep_2' 				 => ['parent' => 'site', 'separator' => true],
	'site_media' 			 	 => ['parent' => 'site', 'title' => App::__('menu_media'), 'block' => 'admin-media', 'url' => $this->app->uri->getAdminBlock('admin_media')],


	'content' 					 => ['parent' => '', 'title' => App::__('menu_content'), 'block' => 'admin-', 'url' => '', ],
	'content_new_page'		 => ['parent' => 'content', 'title' => App::__('menu_new_page'), 'block' => 'admin-pages', 'url' => $this->app->uri->getAdminBlock('admin_pages', 'add')],
	'content_pages'			 => ['parent' => 'content', 'title' => App::__('menu_pages'), 'block' => 'admin-pages', $this->app->uri->getAdminBlock('admin_pages')],
	'content_sep_1' 			 => ['parent' => 'content', 'separator' => true],
	/*'content_menu'				 => ['parent' => 'content', 'title' => App::__('menu_menu'), 'block' => 'admin-menu', 'url' => $venus->admin_index . '?' . $block_param . '=admin_menu'],
	'content_announcements'	 => ['parent' => 'content', 'title' => App::__('menu_announcements'), 'block' => 'admin-announcements', 'url' => $venus->admin_index . '?' . $block_param . '=admin_announcements'],
	'content_categories'		 => ['parent' => 'content', 'title' => App::__('menu_categories'), 'block' => 'admin-categories', 'url' => $venus->admin_index . '?' . $block_param . '=admin_categories'],
	'content_sep_2' 			 => ['parent' => 'content', 'separator' => true],
	'content_banners' 		 => ['parent' => 'content', 'title' => App::__('menu_banners'), 'block' => 'admin-banners', 'url' => $venus->admin_index . '?' . $block_param . '=admin_banners'],
	'content_news'				 => ['parent' => 'content', 'title' => App::__('menu_news'), 'block' => 'admin-news', 'url' => $venus->admin_index . '?' . $block_param . '=admin_news'],
	'content_links'			 => ['parent' => 'content', 'title' => App::__('menu_links'), 'block' => 'admin-links', 'url' => $venus->admin_index . '?' . $block_param . '=admin_links'],
	'content_tags'				 => ['parent' => 'content', 'title' => App::__('menu_tags'), 'block' => 'admin-tags', 'url' => $venus->admin_index . '?' . $block_param . '=admin_tags'],
	'content_sep_3' 			 => ['parent' => 'content', 'separator' => true],
	'content_snippets'		 => ['parent' => 'content', 'title' => App::__('menu_snippets'), 'block' => 'admin-snippets', 'url' => $venus->admin_index . '?' . $block_param . '=admin_snippets'],
	'content_templates'		 => ['parent' => 'content', 'title' => App::__('menu_content_templates'), 'block' => 'admin-content_templates', 'url' => $venus->admin_index . '?' . $block_param . '=admin_content_templates'],


	'comments' 					 => ['parent' => '', 'title' => App::__('menu_comments'), 'block' => 'admin-', 'url' => ''],
	'comments_comments' 		 => ['parent' => 'comments', 'title' => App::__('menu_comments'), 'block' => 'admin-comments', 'url' => $venus->admin_index . '?' . $block_param . '=admin_comments'],
	'comments_spam' 			 => ['parent' => 'comments', 'title' => App::__('menu_spam'), 'block' => 'admin-comments', 'url' => $venus->admin_index . '?' . $block_param . '=admin_comments&' . $controller_param . '=spam'],
	*/
/*

	'users' 						 => ['parent' => '', 'title' => App::__('menu_users'), 'block' => 'admin-', 'url' => ''],
	'users_users' 				 => ['parent' => 'users', 'title' => App::__('menu_users2'), 'block' => 'admin-users', 'url' => $venus->admin_index . '?' . $block_param . '=admin_users'],
	'users_users_1' 			 => ['parent' => 'users_users', 'title' => App::__('menu_users3'), 'block' => 'admin-users', 'url' => $venus->admin_index . '?' . $block_param . '=admin_users&' . $controller_param . '=new'],
	'users_users_2' 			 => ['parent' => 'users_users', 'title' => App::__('menu_users4'), 'block' => 'admin-users', 'url' => $venus->admin_index . '?' . $block_param . '=admin_users&' . $controller_param . '=deactivated'],
	'users_users_3' 			 => ['parent' => 'users_users', 'title' => App::__('menu_users5'), 'block' => 'admin-users', 'url' => $venus->admin_index . '?' . $block_param . '=admin_users&' . $controller_param . '=disabled'],
	'users_usergroups' 		 => ['parent' => 'users', 'title' => App::__('menu_usergroups'), 'block' => 'admin-usergroups', 'url' => $venus->admin_index . '?' . $block_param . '=admin_usergroups'],
	'users_administrators' 	 => ['parent' => 'users', 'title' => App::__('menu_administrators'), 'block' => 'admin-administrators', 'url' => $venus->admin_index . '?' . $block_param . '=admin_administrators'],
	'users_moderators' 		 => ['parent' => 'users', 'title' => App::__('menu_moderators'), 'block' => 'admin-moderators', 'url' => $venus->admin_index . '?' . $block_param . '=admin_moderators'],
	'users_sep_1' 				 => ['parent' => 'users', 'separator' => true],
	'users_new_user' 			 => ['parent' => 'users', 'title' => App::__('menu_new_user'), 'block' => 'admin-users', 'url' => $venus->admin_index . '?' . $block_param . '=admin_users&action=add'],
	'users_merge' 				 => ['parent' => 'users', 'title' => App::__('menu_merge_users'), 'block' => 'admin-users', 'url' => $venus->admin_index . '?' . $block_param . '=admin_users&' . $controller_param . '=merge'],
	'users_sep_2' 				 => ['parent' => 'users', 'separator' => true],
	'users_spammers' 			 => ['parent' => 'users', 'title' => App::__('menu_spammers'), 'block' => 'admin-users', 'url' => $venus->admin_index . '?' . $block_param . '=admin_users&' . $controller_param . '=spammers'],
	'users_banned' 			 => ['parent' => 'users', 'title' => App::__('menu_banned'), 'block' => 'admin-users', 'url' => $venus->admin_index . '?' . $block_param . '=admin_ban_users'],


	'blocks' 					 => ['parent' => '', 'title' => App::__('menu_blocks'), 'block' => 'admin-blocks', 'url' => ''],
	'blocks_blocks' 			 => ['parent' => 'blocks', 'title' => App::__('menu_blocks2'), 'block' => 'admin-blocks', 'url' => $venus->admin_index . '?' . $block_param . '=admin_blocks'],
	'blocks_admin_blocks' 	 => ['parent' => 'blocks', 'title' => App::__('menu_admin_blocks'), 'block' => 'admin-admin_blocks', 'url' => $venus->admin_index . '?' . $block_param . '=admin_admin_blocks'],


	'widgets' 					 => ['parent' => '', 'title' => App::__('menu_widgets'), 'block' => 'admin-widgets', 'url' => ''],
	'widgets_widgets' 		 => ['parent' => 'widgets', 'title' => App::__('menu_widgets2'), 'block' => 'admin-widgets', 'url' => $venus->admin_index . '?' . $block_param . '=admin_widgets'],
*/

	'plugins' 					 => ['parent' => '', 'title' => App::__('menu_plugins'), 'block' => 'plugins', 'url' => ''],
	'plugins_plugins' 		 => ['parent' => 'plugins', 'title' => App::__('menu_plugins2'), 'block' => 'plugins', 'url' => $this->app->uri->getAdminBlock('plugins')],
/*
	'tools' 						 => ['parent' => '', 'title' => App::__('menu_tools'), 'block' => 'admin-', 'url' => ''],
	'tools_newsletter' 		 => ['parent' => 'tools', 'title' => App::__('menu_newsletter'), 'block' => 'admin-newsletter', 'url' => $venus->admin_index . '?' . $block_param . '=admin_newsletter'],
	'tools_smilies' 			 => ['parent' => 'tools', 'title' => App::__('menu_smilies'), 'block' => 'admin-smilies', 'url' => $venus->admin_index . '?' . $block_param . '=admin_smilies'],
	'tools_bad_words' 		 => ['parent' => 'tools', 'title' => App::__('menu_bad_words'), 'block' => 'admin-bad_words', 'url' => $venus->admin_index . '?' . $block_param . '=admin_bad_words'],
	'tools_sep_1' 				 => ['parent' => 'tools', 'separator' => true],
	'tools_robots' 			 => ['parent' => 'tools', 'title' => App::__('menu_robots'), 'block' => 'admin-robotstxt_editor', 'url' => $venus->admin_index . '?' . $block_param . '=admin_robotstxt_editor'],
	'tools_404' 				 => ['parent' => 'tools', 'title' => App::__('menu_404'), 'block' => 'admin-404_editor', 'url' => $venus->admin_index . '?' . $block_param . '=admin_404_editor'],
	'tools_sep_2' 				 => ['parent' => 'tools', 'separator' => true],
	'tools_search_ip' 		 => ['parent' => 'tools', 'title' => App::__('menu_search_ip'), 'block' => 'admin-search_ip', 'url' => $venus->admin_index . '?' . $block_param . '=admin_search_ip'],
	'tools_ban_ip' 			 => ['parent' => 'tools', 'title' => App::__('menu_ban_ip'), 'block' => 'admin-ban_ip', 'url' => $venus->admin_index . '?' . $block_param . '=admin_ban_ip'],
	'tools_sep_3' 				 => ['parent' => 'tools', 'separator' => true],
	'tools_db_backup' 		 => ['parent' => 'tools', 'title' => App::__('menu_db_backup'), 'block' => 'admin-db_backup', 'url' => $venus->admin_index . '?' . $block_param . '=admin_db_backup'],
	*/

];
