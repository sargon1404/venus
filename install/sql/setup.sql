create table venus_config
(
	`name`   								varchar(255)							NOT NULL,
	`value`    								mediumtext								NOT NULL,
	`scope`									varchar(255)							NOT NULL,

	index scope_index(scope(4))
);

create table venus_config_default
(
	`name`   								varchar(255)							NOT NULL,
	`value`    								mediumtext								NOT NULL,
	`scope`									varchar(255)							NOT NULL
);

create table venus_cache
(
	`name`      							varchar(255)							NOT NULL,
	`value`      							longtext									NOT NULL,
	`scope`									varchar(255)							NOT NULL,

	index scope_index(scope(4))
);

create table venus_sessions
(
	`sid`										varchar(40)								NOT NULL,
	`timestamp`								int unsigned							NOT NULL,
	`data`									mediumtext								NOT NULL,

	primary key(sid),
	index timestamp_index(`timestamp`)
);

create table venus_log
(
 	`action`         						text										NOT NULL,
 	`text`        							text										NOT NULL,
 	`uid`										int unsigned							NOT NULL,
 	`ip`                  				varchar(255)					   	NOT NULL,
 	`query_string`							text										NOT NULL,
 	`timestamp`   							int unsigned							NOT NULL,

 	index uid_index(`uid`),
 	index timestamp_index(`timestamp`)
);

create table venus_cli
(
	`command`								varchar(255)					   	NOT NULL,
	`class`									varchar(255)					   	NOT NULL,
	
	index command_index(command(4))
);

create table venus_bruteforce_ips
(
	`ip`               					varchar(255)							NOT NULL,
	`ip_crc`									int unsigned					      NOT NULL,
	`attempts`        					smallint unsigned						NOT NULL,
	`timestamp`   							int unsigned							NOT NULL,
	`scope`									varchar(255)							NOT NULL,

	index ip_index(`ip_crc`, `scope`(4))
);

create table venus_bruteforce_users
(
	`uid`               					int unsigned							NOT NULL,
	`attempts`        					smallint unsigned						NOT NULL,
	`timestamp`   							int unsigned							NOT NULL,
	`scope`									varchar(255)							NOT NULL,

	index user_index(uid, `scope`(4))
);

create table venus_usergroups
(
	`ugid`       							int unsigned auto_increment		NOT NULL,
	`title`         						varchar(255) 		 					NOT NULL,
	`username`								varchar(255) 		 					NOT NULL,
	`status`									tinyint									NOT NULL,
	`is_default`        					tinyint 						 			NOT NULL,
	`note`									varchar(2048)							NOT NULL,
	`markup_tags`							text 							 			NOT NULL,

	`can_upload_avatar` 					tinyint 						 			NOT NULL,
	`avatar`            					varchar(1024)		 					NOT NULL,
	`avatar_original`						varchar(1024)		 					NOT NULL,
	`avatar_for_tablets`					tinyint									NOT NULL,
	`avatar_for_smartphones`			tinyint									NOT NULL,
	`avatar_process`						varchar(255) 		 					NOT NULL,

	`can_comment`    						tinyint 									NOT NULL,
	`comment_interval`    				smallint unsigned						NOT NULL,
	`comment_captcha`    				smallint unsigned						NOT NULL,
	`comment_nofollow`    				smallint	unsigned						NOT NULL,

	`can_rate`    							tinyint 									NOT NULL,
	`rate_interval`    					smallint	unsigned						NOT NULL,

	`created_timestamp`					int unsigned							NOT NULL,
	`created_by`							int unsigned							NOT NULL,
	`modified_timestamp`					int unsigned							NOT NULL,
	`modified_by`							int unsigned							NOT NULL,

	primary key(ugid)
);

create table venus_usergroups_permissions
(
	`type`									varchar(255)							NOT NULL,
	`ugid`									int unsigned							NOT NULL,
	`view`									tinyint 									NOT NULL,
	`add`										tinyint 									NOT NULL,
	`publish`								tinyint 									NOT NULL,
	`publish_own`							tinyint 									NOT NULL,
	`edit`									tinyint 									NOT NULL,
	`edit_own`								tinyint 									NOT NULL,
	`delete`									tinyint 									NOT NULL,
	`delete_own`							tinyint 									NOT NULL,
	`comment`								tinyint 									NOT NULL,
	`rate`									tinyint 									NOT NULL,

	index ugid_index(`ugid`)
);

create table venus_users
(
	`uid`                      		int unsigned auto_increment		NOT NULL,
	`username`              	 		varchar(255)   				   	NOT NULL,
	`email`                   			varchar(255) 					 		NOT NULL,
	`ugid`   								int unsigned					 		NOT NULL,
	`password`              	 		varchar(255)		   				NOT NULL,
	`secret_key`							varchar(255)		   				NOT NULL,

	`seo_alias`								varchar(255)							NOT NULL,

	`status`            					tinyint	 			 					NOT NULL,
	`activated`								tinyint	 			 					NOT NULL,

	`activation_code`     	 			varchar(255)				 		 	NOT NULL,
	`activation_email_sent`     	 	tinyint	 			 					NOT NULL,
	`new_password_code`     	 		varchar(255) 				 			NOT NULL,
	`note`									varchar(2048)							NOT NULL,

	`lang`                				int unsigned							NOT NULL,
	`theme`             					int unsigned							NOT NULL,
	`timezone`         			 		varchar(255)  							NOT NULL,
	`avatar`            					varchar(255)							NOT NULL,

	`receaive_pms` 			 			tinyint 									NOT NULL,
	`receaive_emails` 			 		tinyint 									NOT NULL,
	`receaive_admin_emails` 			tinyint 									NOT NULL,

	`pms_received`					 		int unsigned							NOT NULL,
	`pms_sent`         			 		int unsigned							NOT NULL,
	`pms_unread`     			 			int unsigned							NOT NULL,

	`upload_size`      			 		float										NOT NULL,
	`upload_bandwidth_size`  			float										NOT NULL,

	`comments_count`          			int unsigned							NOT NULL,
	`ratings_count`               	int unsigned							NOT NULL,
	`emails_count`							int unsigned							NOT NULL,

	`registration_type`					varchar(255)							NOT NULL,
	`registration_timestamp`      	int unsigned							NOT NULL,
	`registration_ip`          		varchar(255)							NOT NULL,
	`registration_ip_crc`          	int unsigned							NOT NULL,

	`last_pm`     			 				int unsigned							NOT NULL,
	`last_email`     			 			int unsigned							NOT NULL,
	`last_comment`           			int unsigned							NOT NULL,
	`last_rating`           			int unsigned							NOT NULL,

	primary key(uid),
	index index_username(username(8)),
	index index_email(email(8)),
	index index_registration_ip(`registration_ip_crc`),
	index index_ugid(`ugid`)
);

create table venus_users_usergroups
(
	uid										int unsigned							NOT NULL,
	ugid										int unsigned							NOT NULL,

	index index_uid(uid),
	index index_ugid(ugid)
);

create table venus_users_login_keys
(
	`uid`										int unsigned							NOT NULL,
	`key`										varchar(255)							NOT NULL,
	`key_crc`								int unsigned							NOT NULL,
	`ip`               					varchar(255)							NOT NULL,
	`timestamp`   							int unsigned							NOT NULL,
	`valid_timestamp`   					int unsigned							NOT NULL,
	`scope`									varchar(255)							NOT NULL,

	index login_index(`uid`, `key_crc`, `scope`(4)),
	index valid_index(`valid_timestamp`)
);

create table venus_users_notifications
(
	`uid`										int unsigned							NOT NULL,
	`type`									smallint unsigned						NOT NULL,
	`timestamp`								int unsigned							NOT NULL,

	UNIQUE index uid_index(uid, type)
);

create table venus_users_autologin
(
	`uid`										int unsigned							NOT NULL,
	`key`										varchar(255)							NOT NULL,
	`valid_timestamp`						int unsigned							NOT NULL,

	INDEX uid_index(uid, `key`(4)),
	INDEX timestamp_index(`valid_timestamp`)
);

create table venus_plugins
(
	`pid`                     			int unsigned auto_increment		NOT NULL,
	`title`									varchar(255)							NOT NULL,
	`name`                				varchar(255)							NOT NULL,
	`site_access`							tinyint unsigned						NOT NULL,
	`admin_access`							tinyint unsigned						NOT NULL,
	`global`									tinyint unsigned						NOT NULL,
	`status`									tinyint unsigned						NOT NULL,
	`order`             					int unsigned							NOT NULL,
	`note`                				varchar(2048)							NOT NULL,
	`development`							tinyint unsigned						NOT NULL,
	`params`	            				mediumtext								NOT NULL,

	`created_timestamp`					int unsigned							NOT NULL,
	`created_by`							int unsigned							NOT NULL,
	`modified_timestamp`					int unsigned							NOT NULL,
	`modified_by`							int unsigned							NOT NULL,

	primary key(pid),
	index plugins_index(`status`, `global`, `order`)
);

create table venus_cron
(
	`cid`                    			int unsigned	auto_increment		NOT NULL,
	`title`               				varchar(255)							NOT NULL,
	`name`             	 				varchar(255)							NOT NULL,
	`status`									tinyint unsigned						NOT NULL,
	`next`        			 				int unsigned							NOT NULL,
	`last`                  			int unsigned							NOT NULL,
	`interval_type` 	 					tinyint unsigned						NOT NULL,
	`interval_value`          	 		int unsigned							NOT NULL,
	`note` 									varchar(2048) 							NOT NULL,
	`development`							tinyint unsigned						NOT NULL,
	`params`              				mediumtext								NOT NULL,

	`created_timestamp`					int unsigned							NOT NULL,
	`created_by`							int unsigned							NOT NULL,
	`modified_timestamp`					int unsigned							NOT NULL,
	`modified_by`							int unsigned							NOT NULL,

	primary key(cid),
	index next_index(`status`, `next`)
);

create table venus_languages
(
	`lid`                 				int unsigned auto_increment		NOT NULL,
	`parent`									int unsigned							NOT NULL,
	`title`           					varchar(255)      					NOT NULL,
	`name`           						varchar(255)      					NOT NULL,
	`status`									tinyint unsigned						NOT NULL,
	`note`									varchar(255)             			NOT NULL,
	`development`							tinyint unsigned						NOT NULL,
	`content`								tinyint unsigned						NOT NULL,
	`encoding`     						varchar(255)             			NOT NULL,
	`code`     								varchar(255)             			NOT NULL,
	`url_code`								varchar(255)             			NOT NULL,
	`accept_code`							varchar(255)             			NOT NULL,

	`title_lang`							varchar(255)      					NOT NULL,

	`timestamp_format`     				varchar(255)             			NOT NULL,
	`date_format`     					varchar(255)             			NOT NULL,
	`time_format`     					varchar(255)             			NOT NULL,
	`birthday_format`   					varchar(255)             			NOT NULL,

	`date_picker_format`					varchar(255)             			NOT NULL,
	`time_picker_format`					varchar(255)             			NOT NULL,

	`decimal_separator` 	  				varchar(255)             			NOT NULL,
	`thousands_separator`   			varchar(255)             			NOT NULL,

	`files`									text										NOT NULL,

	`created_timestamp`					int unsigned							NOT NULL,
	`created_by`							int unsigned							NOT NULL,
	`modified_timestamp`					int unsigned							NOT NULL,
	`modified_by`							int unsigned							NOT NULL,

	primary key(lid)
);

create table venus_themes
(
	`tid`                  				int unsigned auto_increment		NOT NULL,
	`parent`									int unsigned							NOT NULL,
	`title`									varchar(255)      					NOT NULL,
	`name`									varchar(255)					  		NOT NULL,
	`status`									tinyint unsigned						NOT NULL,
	`note`									varchar(255)             			NOT NULL,
	`development`							tinyint unsigned						NOT NULL,
	
	`params`									text                             NOT NULL,
	`templates`								text										NOT NULL,
	`layouts`								text										NOT NULL,
	`inline_css`							text										NOT NULL,
	`inline_js`								text										NOT NULL,
	`banner_positions`					text										NOT NULL,
	`widget_positions`					text										NOT NULL,
	`libraries`								mediumtext								NOT NULL,

	`has_javascript_dir`					tinyint unsigned						NOT NULL,
	`has_images_dir`						tinyint unsigned						NOT NULL,
	`has_mobile_images_dir`				tinyint unsigned						NOT NULL,
	`has_tablets_images_dir`			tinyint unsigned						NOT NULL,
	`has_smartphones_images_dir`		tinyint unsigned						NOT NULL,
	`init`									tinyint unsigned						NOT NULL,
	
	`created_timestamp`					int unsigned							NOT NULL,
	`created_by`							int unsigned							NOT NULL,
	`modified_timestamp`					int unsigned							NOT NULL,
	`modified_by`							int unsigned							NOT NULL,
	
	primary key(tid)
);

create table venus_administrators
(
	`uid`										int   				unsigned			NOT NULL,
	`filter`									tinyint				unsigned			NOT NULL,
	`editor`									varchar(255)					   	NOT NULL,
	index uid_index(uid)
);

create table venus_administrators_permissions
(
	`uid`										int   				unsigned			NOT NULL,
	`bid`										int   				unsigned			NOT NULL,
	`view`									tinyint									NOT NULL,
	`add`										tinyint									NOT NULL,
	`publish`								tinyint									NOT NULL,
	`publish_own`							tinyint									NOT NULL,
	`edit`									tinyint									NOT NULL,
	`edit_own`								tinyint									NOT NULL,
	`delete`									tinyint									NOT NULL,
	`delete_own`							tinyint									NOT NULL,
	index uid_index(uid,bid)
);

create table venus_administrators_logins
(
	`uid`										int unsigned							NOT NULL,
	`ip`         							varchar(255)							NOT NULL,
	`useragent`								varchar(255)							NOT NULL,
	`timestamp`   							int unsigned							NOT NULL,

	index timestamp_index(`timestamp`)
);

create table venus_admin_blocks
(
	`bid`                   			int	unsigned 	auto_increment	NOT NULL,
	`title`               				varchar(255) 							NOT NULL,
	`name`               				varchar(255) 							NOT NULL,
	`name_crc`								int 			 		unsigned			NOT NULL,
	`status`									tinyint									NOT NULL,
	`default`								tinyint									NOT NULL,
	`development`							tinyint									NOT NULL,
	`note`									varchar(2048)							NOT NULL,
	`languages`								text										NOT NULL,
	`params` 								mediumtext								NOT NULL,
	`created_timestamp`					int					unsigned			NOT NULL,
 	`created_by`							int					unsigned			NOT NULL,
 	`modified_timestamp`					int					unsigned			NOT NULL,
 	`modified_by`							int					unsigned			NOT NULL,

	primary key(bid),
	index name_crc_index(`name_crc`)
);


create table venus_admin_menu
(
	`mid`     	 							int unsigned	auto_increment  	NOT NULL,
	`type`									varchar(255)							NOT NULL,
	`type_id`								int										NOT NULL,
	`title`				   				varchar(255)							NOT NULL,
	`url`          						text							   		NOT NULL,
	`icon`         	   				varchar(255)							NOT NULL,
	`parent`       						int unsigned							NOT NULL,
	`block`									varchar(255)							NOT NULL,
	primary key(mid)
);