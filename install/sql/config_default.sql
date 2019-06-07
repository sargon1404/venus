INSERT INTO venus_config_default VALUES('upload_allow','avi,mpg,mpeg,mkv,mp4,mp3,doc,docx,odt,ods,odp,odg,flv,jpeg,jpg,bmp,ico,png,gif,pdf,pps,psd,swf,txt,xls,xlsx,zip,rar,ace,tz,gz,htm,html,csv,xml','frontend');

INSERT INTO venus_config_default VALUES('editor','bbcode','frontend');
INSERT INTO venus_config_default VALUES('editor','wysiwyg','admin');

INSERT INTO venus_config_default VALUES('items_per_page','10','frontend');
INSERT INTO venus_config_default VALUES('items_per_page','10','admin');

INSERT INTO venus_config_default VALUES('image_process','resize','frontend');


INSERT INTO venus_config_default VALUES('menu_show','1','frontend');
INSERT INTO venus_config_default VALUES('banners_show','1','frontend');
INSERT INTO venus_config_default VALUES('breadcrumbs_show','1','frontend');
INSERT INTO venus_config_default VALUES('breadcrumbs_home','','frontend');
INSERT INTO venus_config_default VALUES('breadcrumbs_separator','>>','frontend');


INSERT INTO venus_config_default VALUES('mail_type','mail','frontend');
INSERT INTO venus_config_default VALUES('mail_smtp_server','','frontend');
INSERT INTO venus_config_default VALUES('mail_smtp_use_auth','0','frontend');
INSERT INTO venus_config_default VALUES('mail_smtp_auth_username','','frontend');
INSERT INTO venus_config_default VALUES('mail_smtp_auth_password','','frontend');
INSERT INTO venus_config_default VALUES('mail_smtp_port','25','frontend');
INSERT INTO venus_config_default VALUES('mail_smtp_secure','','frontend');

INSERT INTO venus_config_default VALUES('log_days','14','frontend');

INSERT INTO venus_config_default VALUES('plugins_enable','1','frontend');

INSERT INTO venus_config_default VALUES('widgets_enable','1','frontend');
INSERT INTO venus_config_default VALUES('widget_show_title','1','frontend');
INSERT INTO venus_config_default VALUES('widget_cache_interval','604800','frontend');

INSERT INTO venus_config_default VALUES('geoip_enable','0','frontend');

INSERT INTO venus_config_default VALUES('cron_enable','1','frontend');
INSERT INTO venus_config_default VALUES('cron_jobs_per_user','1','frontend');
INSERT INTO venus_config_default VALUES('cron_email_minutes_interval','1','frontend');
INSERT INTO venus_config_default VALUES('cron_email_number','20','frontend');

INSERT INTO venus_config_default VALUES ('ban_ips_enable','1','frontend');
INSERT INTO venus_config_default VALUES ('ban_ips_v4_in_cache','100','frontend');
INSERT INTO venus_config_default VALUES ('ban_ips_v6_in_cache','100','frontend');

INSERT INTO venus_config_default VALUES('token_field','token','frontend');

INSERT INTO venus_config_default VALUES('webstorage_enable',1,'frontend');
INSERT INTO venus_config_default VALUES('webstorage_interval',20,'frontend');

INSERT INTO venus_config_default VALUES('response_param','response','frontend');
INSERT INTO venus_config_default VALUES('controller_param','controller','frontend');
INSERT INTO venus_config_default VALUES('action_param','action','frontend');
INSERT INTO venus_config_default VALUES('return_route_param','return','frontend');
INSERT INTO venus_config_default VALUES('order_param','order','frontend');
INSERT INTO venus_config_default VALUES('orderby_param','orderby','frontend');
INSERT INTO venus_config_default VALUES('block_param','block','frontend');
INSERT INTO venus_config_default VALUES('page_param','page','frontend');
INSERT INTO venus_config_default VALUES('type_param','document_type','frontend');
INSERT INTO venus_config_default VALUES('id_param','document_id','frontend');



INSERT INTO venus_config_default VALUES('media_image_preview_width',100,'frontend');
INSERT INTO venus_config_default VALUES('media_image_preview_height',0,'frontend');

INSERT INTO venus_config_default VALUES('pagination_max_links',10,'frontend');

INSERT INTO venus_config_default VALUES('text_parse_links','1','frontend');



INSERT INTO venus_config_default VALUES('users_min_username','4','frontend');
INSERT INTO venus_config_default VALUES('users_min_password','6','frontend');

INSERT INTO venus_config_default VALUES('registration_enable','1','frontend');
INSERT INTO venus_config_default VALUES('registration_disabled_reason','','frontend');
INSERT INTO venus_config_default VALUES('registration_type','email','frontend');
INSERT INTO venus_config_default VALUES('registration_show_agreement','1','frontend');
INSERT INTO venus_config_default VALUES('registration_inform_admin','1','frontend');
INSERT INTO venus_config_default VALUES('registration_captcha','1','frontend');
INSERT INTO venus_config_default VALUES('registration_per_ip','0','frontend');

INSERT INTO venus_config_default VALUES('login_use_captcha','0','frontend');
INSERT INTO venus_config_default VALUES('login_secure','1','frontend');

INSERT INTO venus_config_default VALUES('bruteforce_ip_max_attemps','5','frontend');
INSERT INTO venus_config_default VALUES('bruteforce_ip_block_seconds','600','frontend');
INSERT INTO venus_config_default VALUES('bruteforce_user_max_attemps','5','frontend');
INSERT INTO venus_config_default VALUES('bruteforce_user_block_seconds','30','frontend');


INSERT INTO venus_config_default VALUES('social_login_enable','0','frontend');



INSERT INTO venus_config_default VALUES('rss_enable','1','frontend');
INSERT INTO venus_config_default VALUES('rss_cache_interval','60','frontend');
INSERT INTO venus_config_default VALUES('rss_interval','4320','frontend');
INSERT INTO venus_config_default VALUES('rss_ttl','','frontend');

INSERT INTO venus_config_default VALUES('sitemap_enable','1','frontend');
INSERT INTO venus_config_default VALUES('sitemap_cache_interval','1440','frontend');
INSERT INTO venus_config_default VALUES('sitemap_gzip','1','frontend');



INSERT INTO venus_config_default VALUES('captcha_enable','0','frontend');


INSERT INTO venus_config_default VALUES('search_enable','1','frontend');
INSERT INTO venus_config_default VALUES('search_engine','like','frontend');
INSERT INTO venus_config_default VALUES('search_minimum_chars','3','frontend');
INSERT INTO venus_config_default VALUES('search_result_chars','400','frontend');
INSERT INTO venus_config_default VALUES('search_max_results','1000','frontend');
INSERT INTO venus_config_default VALUES('search_cache','1','frontend');
INSERT INTO venus_config_default VALUES('search_cache_interval','360','frontend');
INSERT INTO venus_config_default VALUES('search_order_by','relevance','frontend');

INSERT INTO venus_config_default VALUES('search_sphinx_server','localhost','frontend');
INSERT INTO venus_config_default VALUES('search_sphinx_port','9312','frontend');
INSERT INTO venus_config_default VALUES('search_sphinx_match','all','frontend');


INSERT INTO venus_config_default VALUES('seo_enable','0','frontend');
INSERT INTO venus_config_default VALUES('seo_slug_slash','1','frontend');
INSERT INTO venus_config_default VALUES('seo_page_param','/page-{PAGE_NO}','frontend');
INSERT INTO venus_config_default VALUES('seo_user_url','profile/{ALIAS}/u-{ID}.htm','frontend');
INSERT INTO venus_config_default VALUES('seo_page_url','{CATEGORY_PARAM}{ALIAS}{PAGE_PARAM}/p-{ID}.htm','frontend');
INSERT INTO venus_config_default VALUES('seo_block_url','{CATEGORY_PARAM}{ALIAS}{EXTRA}{PAGE_PARAM}/b-{ID}.htm','frontend');
INSERT INTO venus_config_default VALUES('seo_category_url','category/{PARENTS_PARAM}{ALIAS}{PAGE_PARAM}/c-{ID}.htm','frontend');
INSERT INTO venus_config_default VALUES('seo_tag_url','tag/{ALIAS}{PAGE_PARAM}/t-{ID}.htm','frontend');
INSERT INTO venus_config_default VALUES('seo_comments_param','comments_page','frontend');



INSERT INTO venus_config_default VALUES('page_max_versions','5','frontend');
INSERT INTO venus_config_default VALUES('page_read_more_chars',600,'frontend');
INSERT INTO venus_config_default VALUES('page_created_by_limit','1000','frontend');
INSERT INTO venus_config_default VALUES('page_layout','','frontend');
INSERT INTO venus_config_default VALUES('page_comments_open','1','frontend');
INSERT INTO venus_config_default VALUES('page_comments_show_count','1','frontend');
INSERT INTO venus_config_default VALUES('page_comments_per_page','0','frontend');
INSERT INTO venus_config_default VALUES('page_ratings_open','1','frontend');
INSERT INTO venus_config_default VALUES('page_ratings_show_count','1','frontend');
INSERT INTO venus_config_default VALUES('page_show_widgets','1','frontend');
INSERT INTO venus_config_default VALUES('page_show_breadcrumbs','1','frontend');
INSERT INTO venus_config_default VALUES('page_show_title','1','frontend');
INSERT INTO venus_config_default VALUES('page_show_image','1','frontend');
INSERT INTO venus_config_default VALUES('page_show_category','1','frontend');
INSERT INTO venus_config_default VALUES('page_show_author','1','frontend');
INSERT INTO venus_config_default VALUES('page_show_date','1','frontend');
INSERT INTO venus_config_default VALUES('page_show_modified_date','1','frontend');
INSERT INTO venus_config_default VALUES('page_show_tags','1','frontend');
INSERT INTO venus_config_default VALUES('page_show_rating','1','frontend');
INSERT INTO venus_config_default VALUES('page_show_modified_date','1','frontend');
INSERT INTO venus_config_default VALUES('page_show_category_title','1','frontend');
INSERT INTO venus_config_default VALUES('page_show_category_image','1','frontend');
INSERT INTO venus_config_default VALUES('page_show_category_text','1','frontend');
INSERT INTO venus_config_default VALUES('page_seo_rel','','frontend');
INSERT INTO venus_config_default VALUES('page_seo_target','','frontend');
INSERT INTO venus_config_default VALUES('page_meta_robots','index, follow','frontend');
INSERT INTO venus_config_default VALUES('page_cache_comments','0','frontend');
INSERT INTO venus_config_default VALUES('page_cache_comments_interval','3600','frontend');
INSERT INTO venus_config_default VALUES('page_track_hits','1','frontend');


INSERT INTO venus_config_default VALUES('category_layout','','frontend');
INSERT INTO venus_config_default VALUES('category_show_widgets','1','frontend');
INSERT INTO venus_config_default VALUES('category_show_breadcrumbs','1','frontend');
INSERT INTO venus_config_default VALUES('category_show_title','1','frontend');
INSERT INTO venus_config_default VALUES('category_show_parent','1','frontend');
INSERT INTO venus_config_default VALUES('category_show_image','1','frontend');
INSERT INTO venus_config_default VALUES('category_show_description','1','frontend');
INSERT INTO venus_config_default VALUES('category_items_per_page','20','frontend');
INSERT INTO venus_config_default VALUES('category_max_pages_per_page','','frontend');
INSERT INTO venus_config_default VALUES('category_show_subcategories','1','frontend');
INSERT INTO venus_config_default VALUES('category_news_per_page','5','frontend');
INSERT INTO venus_config_default VALUES('category_show_news','1','frontend');
INSERT INTO venus_config_default VALUES('category_show_links','1','frontend');
INSERT INTO venus_config_default VALUES('category_show_blocks','1','frontend');
INSERT INTO venus_config_default VALUES('category_show_pages','1','frontend');
INSERT INTO venus_config_default VALUES('category_subcategories_sort_by','2','frontend');
INSERT INTO venus_config_default VALUES('category_subcategories_sort','','frontend');
INSERT INTO venus_config_default VALUES('category_news_sort_by','2','frontend');
INSERT INTO venus_config_default VALUES('category_news_sort','','frontend');
INSERT INTO venus_config_default VALUES('category_links_sort_by','2','frontend');
INSERT INTO venus_config_default VALUES('category_links_sort','','frontend');
INSERT INTO venus_config_default VALUES('category_blocks_sort_by','2','frontend');
INSERT INTO venus_config_default VALUES('category_blocks_sort','','frontend');
INSERT INTO venus_config_default VALUES('category_pages_sort_by','2','frontend');
INSERT INTO venus_config_default VALUES('category_pages_sort','','frontend');
INSERT INTO venus_config_default VALUES('category_show_category_title','1','frontend');
INSERT INTO venus_config_default VALUES('category_show_category_image','1','frontend');
INSERT INTO venus_config_default VALUES('category_show_category_description','1','frontend');
INSERT INTO venus_config_default VALUES('category_seo_rel','','frontend');
INSERT INTO venus_config_default VALUES('category_seo_target','','frontend');
INSERT INTO venus_config_default VALUES('category_meta_robots','index, follow','frontend');
INSERT INTO venus_config_default VALUES('category_cache','1','frontend');
INSERT INTO venus_config_default VALUES('category_cache_interval','3600','frontend');
INSERT INTO venus_config_default VALUES('category_sitemap_include','1','frontend');
INSERT INTO venus_config_default VALUES('category_sitemap_frequency','1','frontend');
INSERT INTO venus_config_default VALUES('category_sitemap_priority','0.5','frontend');
INSERT INTO venus_config_default VALUES('category_search_include',1,'frontend');
INSERT INTO venus_config_default VALUES('category_rss_enable',1,'frontend');
INSERT INTO venus_config_default VALUES('category_rss_include',1,'frontend');
INSERT INTO venus_config_default VALUES('category_track_hits',1,'frontend');
INSERT INTO venus_config_default VALUES('category_read_more_chars',600,'frontend');


INSERT INTO venus_config_default VALUES('tags_separator',',','frontend');
INSERT INTO venus_config_default VALUES('tag_layout','','frontend');
INSERT INTO venus_config_default VALUES('tag_show_widgets','1','frontend');
INSERT INTO venus_config_default VALUES('tag_show_breadcrumbs','1','frontend');
INSERT INTO venus_config_default VALUES('tag_show_title','1','frontend');
INSERT INTO venus_config_default VALUES('tag_show_image','1','frontend');
INSERT INTO venus_config_default VALUES('tag_show_description','1','frontend');
INSERT INTO venus_config_default VALUES('tag_items_per_page','20','frontend');
INSERT INTO venus_config_default VALUES('tag_show_page_title','1','frontend');
INSERT INTO venus_config_default VALUES('tag_show_page_image','1','frontend');
INSERT INTO venus_config_default VALUES('tag_show_blocks','1','frontend');
INSERT INTO venus_config_default VALUES('tag_show_pages','1','frontend');
INSERT INTO venus_config_default VALUES('tag_blocks_sort_by','2','frontend');
INSERT INTO venus_config_default VALUES('tag_blocks_sort','1','frontend');
INSERT INTO venus_config_default VALUES('tag_pages_sort_by','2','frontend');
INSERT INTO venus_config_default VALUES('tag_pages_sort','1','frontend');
INSERT INTO venus_config_default VALUES('tag_seo_rel','','frontend');
INSERT INTO venus_config_default VALUES('tag_seo_target','','frontend');
INSERT INTO venus_config_default VALUES('tag_meta_robots','index, follow','frontend');
INSERT INTO venus_config_default VALUES('tag_cache','1','frontend');
INSERT INTO venus_config_default VALUES('tag_cache_interval','3600','frontend');
INSERT INTO venus_config_default VALUES('tag_sitemap_include','1','frontend');
INSERT INTO venus_config_default VALUES('tag_sitemap_frequency','1','frontend');
INSERT INTO venus_config_default VALUES('tag_sitemap_priority','0.5','frontend');
INSERT INTO venus_config_default VALUES('tag_track_hits',1,'frontend');


INSERT INTO venus_config_default VALUES('announcements_show','1','frontend');
INSERT INTO venus_config_default VALUES('announcement_show_title','1','frontend');
INSERT INTO venus_config_default VALUES('announcement_show_image','1','frontend');
INSERT INTO venus_config_default VALUES('announcement_show_date','1','frontend');


INSERT INTO venus_config_default VALUES('news_show_title','1','frontend');
INSERT INTO venus_config_default VALUES('news_show_image','1','frontend');
INSERT INTO venus_config_default VALUES('news_show_date','1','frontend');
INSERT INTO venus_config_default VALUES('news_seo_rel','','frontend');
INSERT INTO venus_config_default VALUES('news_seo_target','','frontend');


INSERT INTO venus_config_default VALUES('link_show_title','1','frontend');
INSERT INTO venus_config_default VALUES('link_show_image','1','frontend');
INSERT INTO venus_config_default VALUES('link_seo_rel','','frontend');
INSERT INTO venus_config_default VALUES('link_seo_target','','frontend');


INSERT INTO venus_config_default VALUES('block_read_more_chars',600,'frontend');
INSERT INTO venus_config_default VALUES('block_layout','','frontend');
INSERT INTO venus_config_default VALUES('block_comments_open','1','frontend');
INSERT INTO venus_config_default VALUES('block_comments_show_count','1','frontend');
INSERT INTO venus_config_default VALUES('block_comments_per_page','0','frontend');
INSERT INTO venus_config_default VALUES('block_ratings_open','1','frontend');
INSERT INTO venus_config_default VALUES('block_ratings_show_count','1','frontend');
INSERT INTO venus_config_default VALUES('block_show_widgets','1','frontend');
INSERT INTO venus_config_default VALUES('block_show_breadcrumbs','1','frontend');
INSERT INTO venus_config_default VALUES('block_show_title','1','frontend');
INSERT INTO venus_config_default VALUES('block_show_category','1','frontend');
INSERT INTO venus_config_default VALUES('block_show_image','1','frontend');
INSERT INTO venus_config_default VALUES('block_show_description','1','frontend');
INSERT INTO venus_config_default VALUES('block_show_rating','1','frontend');
INSERT INTO venus_config_default VALUES('block_show_comments','1','frontend');
INSERT INTO venus_config_default VALUES('block_show_tags','1','frontend');
INSERT INTO venus_config_default VALUES('block_show_category_title','1','frontend');
INSERT INTO venus_config_default VALUES('block_show_category_image','1','frontend');
INSERT INTO venus_config_default VALUES('block_show_category_description','1','frontend');
INSERT INTO venus_config_default VALUES('block_seo_rel','','frontend');
INSERT INTO venus_config_default VALUES('block_seo_target','','frontend');
INSERT INTO venus_config_default VALUES('block_meta_robots','index, follow','frontend');
INSERT INTO venus_config_default VALUES('block_cache','0','frontend');
INSERT INTO venus_config_default VALUES('block_cache_front_only','0','frontend');
INSERT INTO venus_config_default VALUES('block_cache_interval','3600','frontend');
INSERT INTO venus_config_default VALUES('block_cache_comments','1','frontend');
INSERT INTO venus_config_default VALUES('block_cache_comments_interval','3600','frontend');
INSERT INTO venus_config_default VALUES('block_sitemap_include','1','frontend');
INSERT INTO venus_config_default VALUES('block_sitemap_frequency','1','frontend');
INSERT INTO venus_config_default VALUES('block_sitemap_priority','0.5','frontend');
INSERT INTO venus_config_default VALUES('block_rss_include',1,'frontend');
INSERT INTO venus_config_default VALUES('block_search_include',1,'frontend');
INSERT INTO venus_config_default VALUES('block_track_hits','1','frontend');



INSERT INTO venus_config_default VALUES('comments_enable','1','frontend');
INSERT INTO venus_config_default VALUES('comments_show_modified','1','frontend');
INSERT INTO venus_config_default VALUES('comments_show_signature','1','frontend');
INSERT INTO venus_config_default VALUES('comments_per_page','0','frontend');
INSERT INTO venus_config_default VALUES('comments_sort','','frontend');

INSERT INTO venus_config_default VALUES('ratings_enable','1','frontend');
INSERT INTO venus_config_default VALUES('ratings_per_item','1','frontend');
INSERT INTO venus_config_default VALUES('ratings_type','1','frontend');
INSERT INTO venus_config_default VALUES('ratings_min','1','frontend');
INSERT INTO venus_config_default VALUES('ratings_max','5','frontend');
