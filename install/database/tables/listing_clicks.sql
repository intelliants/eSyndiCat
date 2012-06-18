{install:drop_tables}DROP TABLE IF EXISTS `{install:prefix}listing_clicks`;
CREATE TABLE `{install:prefix}listing_clicks` (
  `id` int(8) NOT NULL auto_increment,
  `listing_id` int(8) NOT NULL default '0',
  `ip` varchar(16) NOT NULL default '',
  `date` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`id`),
  KEY `listing_id` (`listing_id`),
  KEY `ip` (`ip`,`date`)
) {install:mysql_version};

