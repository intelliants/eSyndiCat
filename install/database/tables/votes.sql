{install:drop_tables}DROP TABLE IF EXISTS `{install:prefix}votes`;
CREATE TABLE `{install:prefix}votes` (
	`listing_id` int(8) NOT NULL default '0',
	`vote_value` tinyint(4) NOT NULL default '0',
	`ip_address` varchar(15) NOT NULL,
	`date` datetime NOT NULL default '0000-00-00 00:00:00',
	KEY `ip_address` (`ip_address`),
	KEY `listing_id` (`listing_id`)
) {install:mysql_version};
