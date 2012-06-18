{install:drop_tables}DROP TABLE IF EXISTS `{install:prefix}comments`;
CREATE TABLE `{install:prefix}comments` (
	`id` int(8) NOT NULL auto_increment,
	`listing_id` int(8) NOT NULL default '0',
	`account_id` int(8) NOT NULL default '0',
	`author` varchar(100) NOT NULL default '',
	`url` varchar(100) NOT NULL default '',
	`body` text NOT NULL,
	`email` varchar(100) NOT NULL default '',
	`ip_address` varchar(15) NOT NULL default '',
	`rating` varchar(50) NOT NULL default '',
	`date` datetime NOT NULL default '0000-00-00 00:00:00',
	`status` enum('inactive', 'active') NOT NULL default 'inactive',
	`sess_id` varchar(32) NOT NULL default '',
	PRIMARY KEY  (`id`),
	KEY `listing_id` (`listing_id`)
) {install:mysql_version};
