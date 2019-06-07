INSERT INTO venus_config VALUES('debug','0','frontend');
INSERT INTO venus_config VALUES('debug_ips','','frontend');
INSERT INTO venus_config VALUES('debug','0','admin');
INSERT INTO venus_config VALUES('debug_ips','','admin');

INSERT INTO venus_config VALUES('log_days','14','frontend');

INSERT INTO venus_config VALUES('site_name','','frontend');
INSERT INTO venus_config VALUES('title','{TITLE}','frontend');
INSERT INTO venus_config VALUES('timezone_default','Europe/London','frontend');

INSERT INTO venus_config VALUES('language_default','1','frontend');
INSERT INTO venus_config VALUES('language_fallback','english','frontend');

INSERT INTO venus_config VALUES('theme_default','1','frontend');

INSERT INTO venus_config VALUES('offline','0','frontend');
INSERT INTO venus_config VALUES('offline_reason','','frontend');

INSERT INTO venus_config VALUES('mail_from','','frontend');
INSERT INTO venus_config VALUES('mail_from_name','','frontend');
INSERT INTO venus_config VALUES('mail_type','mail','frontend');
INSERT INTO venus_config VALUES('mail_smtp_server','','frontend');
INSERT INTO venus_config VALUES('mail_smtp_use_auth','0','frontend');
INSERT INTO venus_config VALUES('mail_smtp_auth_username','','frontend');
INSERT INTO venus_config VALUES('mail_smtp_auth_password','','frontend');
INSERT INTO venus_config VALUES('mail_smtp_port','25','frontend');
INSERT INTO venus_config VALUES('mail_smtp_secure','','frontend');

INSERT INTO venus_config VALUES('upload_allow','avi,mpg,mpeg,mkv,mp4,mp3,doc,docx,odt,ods,odp,odg,flv,jpeg,jpg,bmp,ico,png,gif,pdf,pps,psd,swf,txt,xls,xlsx,zip,rar,ace,tz,gz,htm,html,csv,xml','frontend');

INSERT INTO venus_config VALUES('homepage_type','category','frontend');
INSERT INTO venus_config VALUES('homepage_id',1,'frontend');


INSERT INTO venus_config VALUES('session_save_path','','frontend');
INSERT INTO venus_config VALUES('session_cookie_name','venus-session','frontend');
INSERT INTO venus_config VALUES('session_cookie_path','','frontend');
INSERT INTO venus_config VALUES('session_cookie_domain','','frontend');

INSERT INTO venus_config VALUES('session_regenerate_interval','10','frontend');
INSERT INTO venus_config VALUES('session_keep_alive','3','frontend');

INSERT INTO venus_config VALUES('session_save_path','','admin');
INSERT INTO venus_config VALUES('session_cookie_name','venus-admin-session','admin');
INSERT INTO venus_config VALUES('session_cookie_path','','frontend');
INSERT INTO venus_config VALUES('session_cookie_domain','','frontend');
INSERT INTO venus_config VALUES('session_regenerate_interval','5','admin');
INSERT INTO venus_config VALUES('session_keep_alive','3','admin');


INSERT INTO venus_config VALUES('users_enable','1','frontend');
INSERT INTO venus_config VALUES('user_cookie_name','venus-user','frontend');
INSERT INTO venus_config VALUES('user_cookie_expires','43200','frontend');
INSERT INTO venus_config VALUES('user_autologin_expires','5','frontend');

INSERT INTO venus_config VALUES('user_cookie_name','venus-admin-user','admin');
INSERT INTO venus_config VALUES('user_cookie_expires','0','admin');


INSERT INTO venus_config VALUES('usergroup_multiple_permissions','1','frontend');


INSERT INTO venus_config VALUES('css_minify','1','frontend');
INSERT INTO venus_config VALUES('css_merge','1','frontend');
INSERT INTO venus_config VALUES('css_location','head','frontend');

INSERT INTO venus_config VALUES('css_minify','1','admin');
INSERT INTO venus_config VALUES('css_merge','1','admin');


INSERT INTO venus_config VALUES('javascript_minify','1','frontend');
INSERT INTO venus_config VALUES('javascript_merge','1','frontend');
INSERT INTO venus_config VALUES('javascript_location','head','frontend');

INSERT INTO venus_config VALUES('javascript_minify','1','admin');
INSERT INTO venus_config VALUES('javascript_merge','1','admin');





INSERT INTO venus_config VALUES('contact_emails','','frontend');
INSERT INTO venus_config VALUES('contact_captcha','0','frontend');
INSERT INTO venus_config VALUES('contact_captcha_guests_only','1','frontend');






INSERT INTO venus_config VALUES('editor','bbcode','frontend');
INSERT INTO venus_config VALUES('editor','wysiwyg','admin');

INSERT INTO venus_config VALUES('items_per_page','10','frontend');
INSERT INTO venus_config VALUES('items_per_page','10','admin');

INSERT INTO venus_config VALUES('image_process','cut_resize','frontend');

INSERT INTO venus_config VALUES('menu_show','1','frontend');
INSERT INTO venus_config VALUES('banners_show','1','frontend');
INSERT INTO venus_config VALUES('breadcrumbs_show','1','frontend');
INSERT INTO venus_config VALUES('breadcrumbs_home','','frontend');
INSERT INTO venus_config VALUES('breadcrumbs_separator','>>','frontend');




INSERT INTO venus_config VALUES('plugins_enable','1','frontend');

INSERT INTO venus_config VALUES('widgets_enable','1','frontend');
INSERT INTO venus_config VALUES('widget_show_title','1','frontend');
INSERT INTO venus_config VALUES('widget_cache_interval','604800','frontend');

INSERT INTO venus_config VALUES('geoip_enable','0','frontend');

INSERT INTO venus_config VALUES('cron_enable','1','frontend');
INSERT INTO venus_config VALUES('cron_jobs_per_user','1','frontend');
INSERT INTO venus_config VALUES('cron_email_minutes_interval','1','frontend');
INSERT INTO venus_config VALUES('cron_email_number','20','frontend');

INSERT INTO venus_config VALUES ('ban_ips_enable','1','frontend');
INSERT INTO venus_config VALUES ('ban_ips_v4_in_cache','100','frontend');
INSERT INTO venus_config VALUES ('ban_ips_v6_in_cache','100','frontend');

INSERT INTO venus_config VALUES('token_field','token','frontend');



INSERT INTO venus_config VALUES('response_param','response','frontend');
INSERT INTO venus_config VALUES('controller_param','controller','frontend');
INSERT INTO venus_config VALUES('action_param','action','frontend');
INSERT INTO venus_config VALUES('return_route_param','return','frontend');
INSERT INTO venus_config VALUES('order_param','order','frontend');
INSERT INTO venus_config VALUES('orderby_param','orderby','frontend');
INSERT INTO venus_config VALUES('block_param','block','frontend');
INSERT INTO venus_config VALUES('page_param','page','frontend');
INSERT INTO venus_config VALUES('type_param','document_type','frontend');
INSERT INTO venus_config VALUES('id_param','document_id','frontend');






INSERT INTO venus_config VALUES('main_js_location','first','frontend');
INSERT INTO venus_config VALUES('main_css_location','first','frontend');

INSERT INTO venus_config VALUES('jquery_location','first','frontend');
INSERT INTO venus_config VALUES('jquery_ui_location','first','frontend');
INSERT INTO venus_config VALUES('jquery_ui_dependencies_location','first','frontend');









INSERT INTO venus_config VALUES('media_image_preview_width',100,'frontend');
INSERT INTO venus_config VALUES('media_image_preview_height',0,'frontend');



INSERT INTO venus_config VALUES('pagination_max_links',10,'frontend');

INSERT INTO venus_config VALUES('text_parse_links','1','frontend');


INSERT INTO venus_config VALUES('users_min_username','4','frontend');
INSERT INTO venus_config VALUES('users_min_password','6','frontend');

INSERT INTO venus_config VALUES('registration_enable','1','frontend');
INSERT INTO venus_config VALUES('registration_disabled_reason','','frontend');
INSERT INTO venus_config VALUES('registration_type','email','frontend');
INSERT INTO venus_config VALUES('registration_show_agreement','1','frontend');
INSERT INTO venus_config VALUES('registration_inform_admin','1','frontend');
INSERT INTO venus_config VALUES('registration_inform_admin_emails','','frontend');
INSERT INTO venus_config VALUES('registration_captcha','1','frontend');
INSERT INTO venus_config VALUES('registration_per_ip','0','frontend');

INSERT INTO venus_config VALUES('login_use_captcha','0','frontend');
INSERT INTO venus_config VALUES('login_secure','1','frontend');
INSERT INTO venus_config VALUES('login_remember_me','1','frontend');

INSERT INTO venus_config VALUES('bruteforce_ip_max_attemps','5','frontend');
INSERT INTO venus_config VALUES('bruteforce_ip_block_seconds','600','frontend');
INSERT INTO venus_config VALUES('bruteforce_user_max_attemps','5','frontend');
INSERT INTO venus_config VALUES('bruteforce_user_block_seconds','30','frontend');

INSERT INTO venus_config VALUES('bruteforce_ip_max_attemps','3','admin');
INSERT INTO venus_config VALUES('bruteforce_ip_block_seconds','600','admin');
INSERT INTO venus_config VALUES('bruteforce_user_max_attemps','3','admin');
INSERT INTO venus_config VALUES('bruteforce_user_block_seconds','600','admin');

INSERT INTO venus_config VALUES('rss_enable','1','frontend');
INSERT INTO venus_config VALUES('rss_cache_interval','60','frontend');
INSERT INTO venus_config VALUES('rss_interval','4320','frontend');
INSERT INTO venus_config VALUES('rss_title','','frontend');
INSERT INTO venus_config VALUES('rss_description','','frontend');
INSERT INTO venus_config VALUES('rss_copyright','','frontend');
INSERT INTO venus_config VALUES('rss_email','','frontend');
INSERT INTO venus_config VALUES('rss_email2','','frontend');
INSERT INTO venus_config VALUES('rss_ttl','','frontend');

INSERT INTO venus_config VALUES('sitemap_enable','1','frontend');
INSERT INTO venus_config VALUES('sitemap_cache_interval','1440','frontend');
INSERT INTO venus_config VALUES('sitemap_gzip','1','frontend');


INSERT INTO venus_config VALUES('captcha_enable','0','frontend');
INSERT INTO venus_config VALUES('captcha_driver','recaptcha','frontend');
INSERT INTO venus_config VALUES('captcha_recaptcha_public_key','','frontend');
INSERT INTO venus_config VALUES('captcha_recaptcha_private_key','','frontend');


INSERT INTO venus_config VALUES('search_enable','1','frontend');
INSERT INTO venus_config VALUES('search_engine','like','frontend');
INSERT INTO venus_config VALUES('search_minimum_chars','3','frontend');
INSERT INTO venus_config VALUES('search_result_chars','400','frontend');
INSERT INTO venus_config VALUES('search_max_results','1000','frontend');
INSERT INTO venus_config VALUES('search_cache','1','frontend');
INSERT INTO venus_config VALUES('search_cache_interval','360','frontend');
INSERT INTO venus_config VALUES('search_order_by','relevance','frontend');

INSERT INTO venus_config VALUES('search_sphinx_server','localhost','frontend');
INSERT INTO venus_config VALUES('search_sphinx_port','9312','frontend');
INSERT INTO venus_config VALUES('search_sphinx_match','all','frontend');


INSERT INTO venus_config VALUES('seo_enable','0','frontend');
INSERT INTO venus_config VALUES('seo_slug_slash','1','frontend');
INSERT INTO venus_config VALUES('seo_page_param','/page-{PAGE_NO}','frontend');
INSERT INTO venus_config VALUES('seo_user_url','profile/{ALIAS}/u-{ID}.htm','frontend');
INSERT INTO venus_config VALUES('seo_page_url','{CATEGORY_PARAM}{ALIAS}{PAGE_PARAM}/p-{ID}.htm','frontend');
INSERT INTO venus_config VALUES('seo_block_url','{CATEGORY_PARAM}{ALIAS}{EXTRA}{PAGE_PARAM}/b-{ID}.htm','frontend');
INSERT INTO venus_config VALUES('seo_category_url','category/{PARENTS_PARAM}{ALIAS}{PAGE_PARAM}/c-{ID}.htm','frontend');
INSERT INTO venus_config VALUES('seo_tag_url','tag/{ALIAS}{PAGE_PARAM}/t-{ID}.htm','frontend');
INSERT INTO venus_config VALUES('seo_comments_param','comments_page','frontend');



INSERT INTO venus_config VALUES('page_max_versions','5','frontend');
INSERT INTO venus_config VALUES('page_read_more_chars',600,'frontend');
INSERT INTO venus_config VALUES('page_created_by_limit','1000','frontend');
INSERT INTO venus_config VALUES('page_layout','','frontend');
INSERT INTO venus_config VALUES('page_comments_open','1','frontend');
INSERT INTO venus_config VALUES('page_comments_show_count','1','frontend');
INSERT INTO venus_config VALUES('page_comments_per_page','0','frontend');
INSERT INTO venus_config VALUES('page_ratings_open','1','frontend');
INSERT INTO venus_config VALUES('page_ratings_show_count','1','frontend');
INSERT INTO venus_config VALUES('page_show_widgets','1','frontend');
INSERT INTO venus_config VALUES('page_show_breadcrumbs','1','frontend');
INSERT INTO venus_config VALUES('page_show_title','1','frontend');
INSERT INTO venus_config VALUES('page_show_image','1','frontend');
INSERT INTO venus_config VALUES('page_show_category','1','frontend');
INSERT INTO venus_config VALUES('page_show_author','1','frontend');
INSERT INTO venus_config VALUES('page_show_date','1','frontend');
INSERT INTO venus_config VALUES('page_show_modified_date','1','frontend');
INSERT INTO venus_config VALUES('page_show_tags','1','frontend');
INSERT INTO venus_config VALUES('page_show_rating','1','frontend');
INSERT INTO venus_config VALUES('page_show_comments','1','frontend');
INSERT INTO venus_config VALUES('page_show_category_title','1','frontend');
INSERT INTO venus_config VALUES('page_show_category_image','1','frontend');
INSERT INTO venus_config VALUES('page_show_category_text','1','frontend');
INSERT INTO venus_config VALUES('page_show_category_category','1','frontend');
INSERT INTO venus_config VALUES('page_show_category_author','1','frontend');
INSERT INTO venus_config VALUES('page_show_category_date','1','frontend');
INSERT INTO venus_config VALUES('page_show_category_modified_date','1','frontend');
INSERT INTO venus_config VALUES('page_show_category_rating','1','frontend');
INSERT INTO venus_config VALUES('page_seo_rel','','frontend');
INSERT INTO venus_config VALUES('page_seo_target','','frontend');
INSERT INTO venus_config VALUES('page_meta_author','','frontend');
INSERT INTO venus_config VALUES('page_meta_robots','index, follow','frontend');
INSERT INTO venus_config VALUES('page_cache_comments','0','frontend');
INSERT INTO venus_config VALUES('page_cache_comments_interval','3600','frontend');
INSERT INTO venus_config VALUES('page_track_hits','1','frontend');


INSERT INTO venus_config VALUES('category_layout','','frontend');
INSERT INTO venus_config VALUES('category_show_widgets','1','frontend');
INSERT INTO venus_config VALUES('category_show_breadcrumbs','1','frontend');
INSERT INTO venus_config VALUES('category_show_title','1','frontend');
INSERT INTO venus_config VALUES('category_show_parent','1','frontend');
INSERT INTO venus_config VALUES('category_show_image','1','frontend');
INSERT INTO venus_config VALUES('category_show_description','1','frontend');
INSERT INTO venus_config VALUES('category_items_per_page','20','frontend');
INSERT INTO venus_config VALUES('category_max_pages_per_page','','frontend');
INSERT INTO venus_config VALUES('category_news_per_page','5','frontend');
INSERT INTO venus_config VALUES('category_show_subcategories','1','frontend');
INSERT INTO venus_config VALUES('category_show_news','1','frontend');
INSERT INTO venus_config VALUES('category_show_links','1','frontend');
INSERT INTO venus_config VALUES('category_show_blocks','1','frontend');
INSERT INTO venus_config VALUES('category_show_pages','1','frontend');
INSERT INTO venus_config VALUES('category_subcategories_sort_by','2','frontend');
INSERT INTO venus_config VALUES('category_subcategories_sort','','frontend');
INSERT INTO venus_config VALUES('category_news_sort_by','2','frontend');
INSERT INTO venus_config VALUES('category_news_sort','','frontend');
INSERT INTO venus_config VALUES('category_links_sort_by','2','frontend');
INSERT INTO venus_config VALUES('category_links_sort','','frontend');
INSERT INTO venus_config VALUES('category_blocks_sort_by','2','frontend');
INSERT INTO venus_config VALUES('category_blocks_sort','','frontend');
INSERT INTO venus_config VALUES('category_pages_sort_by','2','frontend');
INSERT INTO venus_config VALUES('category_pages_sort','','frontend');
INSERT INTO venus_config VALUES('category_show_category_title','1','frontend');
INSERT INTO venus_config VALUES('category_show_category_image','1','frontend');
INSERT INTO venus_config VALUES('category_show_category_description','1','frontend');
INSERT INTO venus_config VALUES('category_seo_rel','','frontend');
INSERT INTO venus_config VALUES('category_seo_target','','frontend');
INSERT INTO venus_config VALUES('category_meta_author','','frontend');
INSERT INTO venus_config VALUES('category_meta_robots','index, follow','frontend');
INSERT INTO venus_config VALUES('category_cache','1','frontend');
INSERT INTO venus_config VALUES('category_cache_interval','3600','frontend');
INSERT INTO venus_config VALUES('category_memcache_pages_pages','10','frontend');
INSERT INTO venus_config VALUES('category_sitemap_include','1','frontend');
INSERT INTO venus_config VALUES('category_sitemap_frequency','1','frontend');
INSERT INTO venus_config VALUES('category_sitemap_priority','0.5','frontend');
INSERT INTO venus_config VALUES('category_search_include',1,'frontend');
INSERT INTO venus_config VALUES('category_rss_enable',1,'frontend');
INSERT INTO venus_config VALUES('category_rss_include',1,'frontend');
INSERT INTO venus_config VALUES('category_track_hits',1,'frontend');
INSERT INTO venus_config VALUES('category_read_more_chars',600,'frontend');


INSERT INTO venus_config VALUES('tags_separator',',','frontend');
INSERT INTO venus_config VALUES('tag_layout','','frontend');
INSERT INTO venus_config VALUES('tag_show_widgets','1','frontend');
INSERT INTO venus_config VALUES('tag_show_breadcrumbs','1','frontend');
INSERT INTO venus_config VALUES('tag_show_title','1','frontend');
INSERT INTO venus_config VALUES('tag_show_image','1','frontend');
INSERT INTO venus_config VALUES('tag_show_description','1','frontend');
INSERT INTO venus_config VALUES('tag_items_per_page','20','frontend');
INSERT INTO venus_config VALUES('tag_show_page_title','1','frontend');
INSERT INTO venus_config VALUES('tag_show_page_image','1','frontend');
INSERT INTO venus_config VALUES('tag_show_blocks','1','frontend');
INSERT INTO venus_config VALUES('tag_show_pages','1','frontend');
INSERT INTO venus_config VALUES('tag_blocks_sort_by','2','frontend');
INSERT INTO venus_config VALUES('tag_blocks_sort','1','frontend');
INSERT INTO venus_config VALUES('tag_pages_sort_by','2','frontend');
INSERT INTO venus_config VALUES('tag_pages_sort','1','frontend');
INSERT INTO venus_config VALUES('tag_seo_rel','','frontend');
INSERT INTO venus_config VALUES('tag_seo_target','','frontend');
INSERT INTO venus_config VALUES('tag_meta_author','','frontend');
INSERT INTO venus_config VALUES('tag_meta_robots','index, follow','frontend');
INSERT INTO venus_config VALUES('tag_cache','1','frontend');
INSERT INTO venus_config VALUES('tag_cache_interval','3600','frontend');
INSERT INTO venus_config VALUES('tag_memcache_pages_pages','10','frontend');
INSERT INTO venus_config VALUES('tag_sitemap_include','1','frontend');
INSERT INTO venus_config VALUES('tag_sitemap_frequency','1','frontend');
INSERT INTO venus_config VALUES('tag_sitemap_priority','0.5','frontend');
INSERT INTO venus_config VALUES('tag_track_hits',1,'frontend');


INSERT INTO venus_config VALUES('announcements_show','1','frontend');
INSERT INTO venus_config VALUES('announcement_show_title','1','frontend');
INSERT INTO venus_config VALUES('announcement_show_image','1','frontend');
INSERT INTO venus_config VALUES('announcement_show_date','1','frontend');


INSERT INTO venus_config VALUES('news_show_title','1','frontend');
INSERT INTO venus_config VALUES('news_show_image','1','frontend');
INSERT INTO venus_config VALUES('news_show_date','1','frontend');
INSERT INTO venus_config VALUES('news_seo_rel','','frontend');
INSERT INTO venus_config VALUES('news_seo_target','','frontend');


INSERT INTO venus_config VALUES('link_show_title','1','frontend');
INSERT INTO venus_config VALUES('link_show_image','1','frontend');
INSERT INTO venus_config VALUES('link_seo_rel','','frontend');
INSERT INTO venus_config VALUES('link_seo_target','','frontend');


INSERT INTO venus_config VALUES('menu_seo_rel','','frontend');
INSERT INTO venus_config VALUES('menu_seo_target','','frontend');


INSERT INTO venus_config VALUES('block_read_more_chars',600,'frontend');
INSERT INTO venus_config VALUES('block_layout','','frontend');
INSERT INTO venus_config VALUES('block_comments_open','1','frontend');
INSERT INTO venus_config VALUES('block_comments_show_count','1','frontend');
INSERT INTO venus_config VALUES('block_comments_per_page','0','frontend');
INSERT INTO venus_config VALUES('block_ratings_open','1','frontend');
INSERT INTO venus_config VALUES('block_ratings_show_count','1','frontend');
INSERT INTO venus_config VALUES('block_show_widgets','1','frontend');
INSERT INTO venus_config VALUES('block_show_breadcrumbs','1','frontend');
INSERT INTO venus_config VALUES('block_show_title','1','frontend');
INSERT INTO venus_config VALUES('block_show_category','1','frontend');
INSERT INTO venus_config VALUES('block_show_image','1','frontend');
INSERT INTO venus_config VALUES('block_show_description','1','frontend');
INSERT INTO venus_config VALUES('block_show_rating','1','frontend');
INSERT INTO venus_config VALUES('block_show_comments','1','frontend');
INSERT INTO venus_config VALUES('block_show_tags','1','frontend');
INSERT INTO venus_config VALUES('block_show_category_title','1','frontend');
INSERT INTO venus_config VALUES('block_show_category_image','1','frontend');
INSERT INTO venus_config VALUES('block_show_category_description','1','frontend');
INSERT INTO venus_config VALUES('block_seo_rel','','frontend');
INSERT INTO venus_config VALUES('block_seo_target','','frontend');
INSERT INTO venus_config VALUES('block_meta_author','','frontend');
INSERT INTO venus_config VALUES('block_meta_robots','index, follow','frontend');
INSERT INTO venus_config VALUES('block_cache','0','frontend');
INSERT INTO venus_config VALUES('block_cache_interval','3600','frontend');
INSERT INTO venus_config VALUES('block_cache_comments','1','frontend');
INSERT INTO venus_config VALUES('block_cache_comments_interval','3600','frontend');
INSERT INTO venus_config VALUES('block_sitemap_include','1','frontend');
INSERT INTO venus_config VALUES('block_sitemap_frequency','1','frontend');
INSERT INTO venus_config VALUES('block_sitemap_priority','0.5','frontend');
INSERT INTO venus_config VALUES('block_rss_include',1,'frontend');
INSERT INTO venus_config VALUES('block_search_include',1,'frontend');
INSERT INTO venus_config VALUES('block_track_hits','1','frontend');



INSERT INTO venus_config VALUES('comments_enable','1','frontend');
INSERT INTO venus_config VALUES('comments_show_modified','1','frontend');
INSERT INTO venus_config VALUES('comments_show_signature','1','frontend');
INSERT INTO venus_config VALUES('comments_per_page','0','frontend');
INSERT INTO venus_config VALUES('comments_sort','','frontend');


INSERT INTO venus_config VALUES('ratings_enable','1','frontend');
INSERT INTO venus_config VALUES('ratings_per_item','1','frontend');
INSERT INTO venus_config VALUES('ratings_type','1','frontend');
INSERT INTO venus_config VALUES('ratings_min','1','frontend');
INSERT INTO venus_config VALUES('ratings_max','5','frontend');