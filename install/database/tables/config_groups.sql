{install:drop_tables}DROP TABLE IF EXISTS `{install:prefix}config_groups`;
CREATE TABLE IF NOT EXISTS `{install:prefix}config_groups` (
  `id` smallint(6) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  `plugin` varchar(50) NOT NULL default '',
  `title` varchar(150) NOT NULL default '',
  `order` float NOT NULL default '0',
  PRIMARY KEY  (`id`)
) {install:mysql_version};

INSERT INTO `{install:prefix}config_groups` VALUES
(NULL, 'general', '', 'General Configuration', 1),
(NULL, 'categories', '', 'Categories Configuration', 2),
(NULL, 'listings', '', 'Listings Configuration', 3),
(NULL, 'accounts', '', 'Accounts Configuration', 4),
(NULL, 'listing_checking', '', 'Listing Checking', 7),
(NULL, 'mail', '', 'Mail Configuration', 8),
(NULL, 'email_templates', '', 'Email Templates', 9),
(NULL, 'cronjob', '', 'Cronjob Configuration', 10),
(NULL, 'captcha', '', 'Captcha Configuration', 12),
(NULL, 'miscellaneous', '', 'Miscellaneous', '13'),
(NULL, 'comments', 'comments', 'Comments', 14);

