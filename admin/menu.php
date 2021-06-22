<?php
namespace Venus\Admin;

return [

	'dashboard' => ['title' => App::__('menu_dashboard'), 'order' => 0, 'block' => 'dashboard', 'url' => $this->app->admin_index],

	'site' => [
		'title' => App::__('menu_site'),
		'order' => 100,
		'items' => [
			'section-1' => [
				'order' => 100,
				'items' => [
					'menu-1' => [
						'order' => 100,
						'menus' => [
							'site-config' => ['title' => App::__('menu_config'), 'order' => 100, 'block' => 'config', 'url' => $this->app->uri->getAdminBlock('config')],
							'site-sites' => ['title' => App::__('menu_sites'), 'order' => 200, 'block' => 'sites', 'url' => $this->app->uri->getAdminBlock('sites')],
							'site-themes' => ['title' => App::__('menu_themes'), 'order' => 300, 'block' => 'themes', 'url' => $this->app->uri->getAdminBlock('themes')],
							'site-languages' => ['title' => App::__('menu_languages'), 'order' => 400, 'block' => 'languages', 'url' => $this->app->uri->getAdminBlock('languages')],
							'site-cron' => ['title' => App::__('menu_cron'), 'order' => 500, 'block' => 'cron', 'url' => $this->app->uri->getAdminBlock('cron')],
							'site-log' => ['title' => App::__('menu_log'), 'order' => 600, 'block' => 'log', 'url' => $this->app->uri->getAdminBlock('logs')],
							'site-cache' => ['title' => App::__('menu_cache'), 'order' => 700, 'block' => 'cache', 'url' => $this->app->uri->getAdminBlock('cache')],
							'site-package' => ['title' => App::__('menu_package'), 'order' => 800, 'block' => 'package', 'url' => $this->app->uri->getAdminBlock('package')]
						]
					]
				]
			]
		]
	],

	'content'	=> [
		'title' => App::__('menu_content'),
		'order' => 200,
		'items' => [
			'section-1' => [
				'order' => 100,
				'items' => [
					'menu-taxonomies' => [
						'title' => App::__('menu_taxonomies'),
						'order' => 100,
						'menus' => [
							'content-categories' => ['title' => App::__('menu_categories'), 'order' => 100, 'block' => 'categories', 'url' => $this->app->uri->getAdminBlock('categories')],
							'content-tags' => ['title' => App::__('menu_tags'), 'order' => 200, 'block' => 'tagsusers', 'url' => $this->app->uri->getAdminBlock('tags')]
						]
					],

					'menu-content' => [
						'title' => App::__('menu_content'),
						'order' => 200,
						'menus' => [
							'content-menus' => ['title' => App::__('menu_menus'), 'order' => 100, 'block' => 'menus', 'url' => $this->app->uri->getAdminBlock('menus')],
							'content-banners' => ['title' => App::__('menu_banners'), 'order' => 200, 'block' => 'banners', 'url' => $this->app->uri->getAdminBlock('banners')],
							'content-announcements' => ['title' => App::__('menu_announcements'), 'order' => 300, 'block' => 'announcements', 'url' => $this->app->uri->getAdminBlock('announcements')],
							'content-templates' => ['title' => App::__('menu_content_templates'), 'order' => 400, 'block' => 'content_templates', 'url' => $this->app->uri->getAdminBlock('content_templates')],
							'content-modules' => ['title' => App::__('menu_modules'), 'order' => 500, 'block' => 'modules', 'url' => $this->app->uri->getAdminBlock('modules')],
							'content-404' => ['title' => App::__('menu_404'), 'order' => 600, 'block' => '404', 'url' => $this->app->uri->getAdminBlock('404')]
						]
					]
				]
			],
			'section-2' => [
				'order' => 200,
				'items' => [
					'menu-pages' => [
						'title' => App::__('menu_pages'),
						'order' => 100,
						'menus' => [
							'content-new-pages' => ['title' => App::__('menu_new_page'), 'order' => 100, 'block' => 'pages', 'url' => $this->app->uri->getAdminBlock('pages')],
							'content-pages' => ['title' => App::__('menu_pages'), 'order' => 200, 'block' => 'pages', 'url' => $this->app->uri->getAdminBlock('pages')],
						]
					],
					'menu-comments' => [
						'title' => App::__('menu_comments'),
						'order' => 200,
						'menus' => [
							'content-comments_published' => ['title' => App::__('menu_comments_published'), 'order' => 100, 'comments' => 'pages', 'url' => $this->app->uri->getAdminBlock('comments')],
							'content-comments-unpublished' => ['title' => App::__('menu_comments_unpublished'), 'order' => 200, 'comments' => 'pages', 'url' => $this->app->uri->getAdminBlock('comments')],
							'content-comments-spam' => ['title' => App::__('menu_comments_spam'), 'order' => 300, 'comments' => 'pages', 'url' => $this->app->uri->getAdminBlock('comments')],
						]
					],

					'menu-media' => [
						'title' => App::__('menu_media'),
						'order' => 300,
						'menus' => [
							'content-media' => ['title' => App::__('menu_media'), 'order' => 100, 'comments' => 'media', 'url' => $this->app->uri->getAdminBlock('media')],
						]
					]
				]
			]
		]
	],


	'users' => [
		'title' => App::__('menu_users'),
		'order' => 300,
		'items' => [
			'section-1' => [
				'order' => 100,
				'items' => [
					'menu-users' => [
						'title' => App::__('menu_users'),
						'order' => 100,
						'menus' => [
							'users-users' => ['title' => App::__('menu_users_users'), 'order' => 100, 'block' => 'users', 'url' => $this->app->uri->getAdminBlock('users')],
							'users-usergroups' => ['title' => App::__('menu_usergroups'), 'order' => 200, 'block' => 'usergroups', 'url' => $this->app->uri->getAdminBlock('usergroups')],
							'users-administrators' => ['title' => App::__('menu_administrators'), 'order' => 300, 'block' => 'administrators', 'url' => $this->app->uri->getAdminBlock('administrators')],
							'users-moderators' => ['title' => App::__('menu_moderators'), 'order' => 400, 'block' => 'moderators', 'url' => $this->app->uri->getAdminBlock('moderators')]
						]
					],
					'menu-bad-guys' => [
						'title' => App::__('menu_bad_guys'),
						'order' => 200,
						'menus' => [
							'users-spammers' => ['title' => App::__('menu_spammers'), 'order' => 100, 'block' => 'users', 'url' => $this->app->uri->getAdminBlock('users')],
							'users-banned' => ['title' => App::__('menu_banned'), 'order' => 200, 'block' => 'users', 'url' => $this->app->uri->getAdminBlock('users')]
						]
					]
				]
			],
			'section-2' => [
				'order' => 200,
				'items' => [
					'menu-tasks' => [
						'title' => App::__('menu_users_tasks'),
						'order' => 100,
						'menus' => [
							'users-new-user' => ['title' => App::__('menu_new_user'), 'order' => 100, 'block' => 'users', 'url' => $this->app->uri->getAdminBlock('users')],
							'users-ban-user' => ['title' => App::__('menu_ban_user'), 'order' => 200, 'block' => 'users', 'url' => $this->app->uri->getAdminBlock('users')],
							'users-merge-users' => ['title' => App::__('menu_merge_users'), 'order' => 300, 'block' => 'users', 'url' => $this->app->uri->getAdminBlock('users')],
						]
					],
					'menu-newsletters' => [
						'title' => App::__('menu_newsletters'),
						'order' => 200,
						'menus' => [
							'newsletters-newsletters' => ['title' => App::__('menu_newsletters_newsletters'), 'order' => 100, 'block' => 'newsletters', 'url' => $this->app->uri->getAdminBlock('newsletters')],
							'newsletters-lists' => ['title' => App::__('menu_newsletters_lists'), 'order' => 200, 'block' => 'newsletters', 'url' => $this->app->uri->getAdminBlock('newsletters')]
						]
					]
				]
			]
		]
	],


	'blocks' => [
		'title' => App::__('menu_blocks'),
		'order' => 400,
		'items' => [
			'section-1' => [
				'order' => 100,
				'items' => [
					'menu-1' => [
						'order' => 100,
						'menus' => [
							'blocks-blocks' => ['title' => App::__('menu_blocks_blocks'), 'order' => 100, 'block' => 'blocks', 'url' => $this->app->uri->getAdminBlock('blocks')],
							'blocks-blocks-admin' => ['title' => App::__('menu_blocks_admin'), 'order' => 200, 'block' => 'admin-blocks', 'url' => $this->app->uri->getAdminBlock('admin-blocks')]
						]
					]
				]
			]
		]
	],


	'widgets' => [
		'title' => App::__('menu_widgets'),
		'order' => 500,
		'items' => [
			'section-1' => [
				'order' => 100,
				'items' => [
					'menu-1' => [
						'order' => 100,
						'menus' => [
							'widgets-widgets' => ['title' => App::__('menu_widgets_widgets'), 'order' => 100, 'block' => 'widgets', 'url' => $this->app->uri->getAdminBlock('widgets')],
							'widgets-groups' => ['title' => App::__('menu_widgets_groups'), 'order' => 200, 'block' => 'widgets', 'url' => $this->app->uri->getAdminBlock('widgets')]
						]
					]
				]
			]
		]
	],


	'plugins' => [
		'title' => App::__('menu_plugins'),
		'order' => 600,
		'items' => [
			'section-1' => [
				'order' => 100,
				'items' => [
					'menu-1' => [
						'order' => 100,
						'menus' => [
							'plugins-plugins' => ['title' => App::__('menu_plugins_plugins'), 'order' => 100, 'block' => 'plugins', 'url' => $this->app->uri->getAdminBlock('plugins')],
						]
					]
				]
			]
		]
	],


	'tools' => [
		'title' => App::__('menu_tools'),
		'order' => 700,
		'items' => [
			'section-1' => [
				'order' => 100,
				'items' => [
					'menu-1' => [
						'title' => App::__('menu_tools_tools'),
						'order' => 100,
						'menus' => [
							'tools-search' => ['title' => App::__('menu_tools_search'), 'order' => 100, 'block' => 'search', 'url' => $this->app->uri->getAdminBlock('search')],
							'tools-rss' => ['title' => App::__('menu_tools_rss'), 'order' => 200, 'block' => 'rss', 'url' => $this->app->uri->getAdminBlock('rss')],
							'tools-sitemap' => ['title' => App::__('menu_tools_sitemap'), 'order' => 300, 'block' => 'sitemap', 'url' => $this->app->uri->getAdminBlock('sitemap')]
						]
					]
				]
			],
			'section-2' => [
				'order' => 200,
				'items' => [
					'menu-ips' => [
						'title' => App::__('menu_tools_ips'),
						'order' => 100,
						'menus' => [
							'tools-ips-search' => ['title' => App::__('menu_tools_search_ip'), 'order' => 100, 'block' => 'ips-search', 'url' => $this->app->uri->getAdminBlock('ips-search')],
							'tools-ips-ban' => ['title' => App::__('menu_tools_ban_ip'), 'order' => 200, 'block' => 'ips-ban', 'url' => $this->app->uri->getAdminBlock('ips-ban')]
						]
					],
					'menu-system' => [
						'title' => App::__('menu_tools_system'),
						'order' => 200,
						'menus' => [
							'tools-backups' => ['title' => App::__('menu_tools_backups'), 'order' => 100, 'block' => 'backups', 'url' => $this->app->uri->getAdminBlock('backups')]
						]
					]
				]
			]
		]
	],
];
