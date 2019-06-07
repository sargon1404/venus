INSERT INTO venus_usergroups VALUES (1, 'Guests', 'Guest', 1, 1, '', 'b,i,u,del,align,list,img,quote,code,smilies,video,preview', 0, '', '', 1, 1, 'resize', 0, 0, 1, 1, 0, 0, UNIX_TIMESTAMP(), 1, UNIX_TIMESTAMP(), 1);
INSERT INTO venus_usergroups VALUES (2, 'Registered', 'Registered User', 1, 1, '', 'all', 1, '', '', 1, 1, 'resize', 1, 30, 0, 1, 1, 60, UNIX_TIMESTAMP(), 1, UNIX_TIMESTAMP(), 1);
INSERT INTO venus_usergroups VALUES (3, 'Spammers', 'Spammer', 1, 1, '', '', 0, '', '', 1, 1, 'resize', 0, 0, 1, 1, 0, 0, UNIX_TIMESTAMP(), 1, UNIX_TIMESTAMP(), 1);
INSERT INTO venus_usergroups VALUES (4, 'Moderators', 'Moderator', 1, 1, '', 'all', 1, '', '', 1, 1, 'resize', 1, 0, 0, 0, 1, 0, UNIX_TIMESTAMP(), 1, UNIX_TIMESTAMP(), 1);
INSERT INTO venus_usergroups VALUES (5, 'Admins', 'Admin', 1, 1, '', 'all', 1, '', '', 1, 1, 'resize', 1, 0, 0, 0, 1, 0, UNIX_TIMESTAMP(), 1, UNIX_TIMESTAMP(), 1);


INSERT INTO venus_usergroups_permissions VALUES ('category', 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO venus_usergroups_permissions VALUES ('announcement', 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO venus_usergroups_permissions VALUES ('block', 1, 1, 0, 0, 0, 0, 0, 0, 0, 1, 0);
INSERT INTO venus_usergroups_permissions VALUES ('news', 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO venus_usergroups_permissions VALUES ('link', 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO venus_usergroups_permissions VALUES ('page', 1, 1, 0, 0, 0, 0, 0, 0, 0, 1, 0);
INSERT INTO venus_usergroups_permissions VALUES ('tag', 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO venus_usergroups_permissions VALUES ('menu', 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO venus_usergroups_permissions VALUES ('banner', 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO venus_usergroups_permissions VALUES ('widget', 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0);

INSERT INTO venus_usergroups_permissions VALUES ('category', 2, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO venus_usergroups_permissions VALUES ('announcement', 2, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO venus_usergroups_permissions VALUES ('block', 2, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO venus_usergroups_permissions VALUES ('news', 2, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO venus_usergroups_permissions VALUES ('link', 2, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO venus_usergroups_permissions VALUES ('page', 2, 1, 0, 0, 0, 0, 0, 0, 0, 1, 1);
INSERT INTO venus_usergroups_permissions VALUES ('tag', 2, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO venus_usergroups_permissions VALUES ('menu', 2, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO venus_usergroups_permissions VALUES ('banner', 2, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO venus_usergroups_permissions VALUES ('widget', 2, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0);

INSERT INTO venus_usergroups_permissions VALUES ('category', 3, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO venus_usergroups_permissions VALUES ('announcement', 3, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO venus_usergroups_permissions VALUES ('block', 3, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO venus_usergroups_permissions VALUES ('news', 3, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO venus_usergroups_permissions VALUES ('link', 3, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO venus_usergroups_permissions VALUES ('page', 3, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO venus_usergroups_permissions VALUES ('tag', 3, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO venus_usergroups_permissions VALUES ('menu', 3, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO venus_usergroups_permissions VALUES ('banner', 3, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0);
INSERT INTO venus_usergroups_permissions VALUES ('widget', 3, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0);

INSERT INTO venus_usergroups_permissions VALUES ('category', 4, 1, 1, 0, 1, 0, 1, 0, 1, 1, 1);
INSERT INTO venus_usergroups_permissions VALUES ('announcement', 4, 1, 1, 0, 1, 0, 1, 0, 1, 1, 1);
INSERT INTO venus_usergroups_permissions VALUES ('block', 4, 1, 1, 0, 1, 0, 1, 0, 1, 1, 1);
INSERT INTO venus_usergroups_permissions VALUES ('news', 4, 1, 1, 0, 1, 0, 1, 0, 1, 1, 1);
INSERT INTO venus_usergroups_permissions VALUES ('link', 4, 1, 1, 0, 1, 0, 1, 0, 1, 1, 1);
INSERT INTO venus_usergroups_permissions VALUES ('page', 4, 1, 1, 0, 1, 0, 1, 0, 1, 1, 1);
INSERT INTO venus_usergroups_permissions VALUES ('tag', 4, 1, 1, 0, 1, 0, 1, 0, 1, 1, 1);
INSERT INTO venus_usergroups_permissions VALUES ('menu', 4, 1, 1, 0, 1, 0, 1, 0, 1, 1, 1);
INSERT INTO venus_usergroups_permissions VALUES ('banner', 4, 1, 1, 0, 1, 0, 1, 0, 1, 1, 1);
INSERT INTO venus_usergroups_permissions VALUES ('widget', 4, 1, 1, 0, 1, 0, 1, 0, 1, 1, 1);

INSERT INTO venus_usergroups_permissions VALUES ('category', 5, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO venus_usergroups_permissions VALUES ('announcement', 5, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO venus_usergroups_permissions VALUES ('block', 5, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO venus_usergroups_permissions VALUES ('news', 5, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO venus_usergroups_permissions VALUES ('link', 5, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO venus_usergroups_permissions VALUES ('page', 5, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO venus_usergroups_permissions VALUES ('tag', 5, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO venus_usergroups_permissions VALUES ('menu', 5, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO venus_usergroups_permissions VALUES ('banner', 5, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO venus_usergroups_permissions VALUES ('widget', 5, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);